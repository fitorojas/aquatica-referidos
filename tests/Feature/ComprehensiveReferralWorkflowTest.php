<?php

namespace Tests\Feature;

use App\Models\BenefitHistory;
use App\Models\Customer;
use App\Models\DuplicateDetection;
use App\Models\Referral;
use App\Models\Service;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveReferralWorkflowTest extends TestCase
{
    protected User $adminUser;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::where('email', 'admin@aquatica.cr')->first();
        $this->service = Service::where('slug', 'bebes-y-ninos-principiantes')->first();
    }

    public function test_public_form_renders_successfully(): void
    {
        $response = $this->get(route('public.referral.create'));
        $response->assertStatus(200);
        $response->assertSee('¡Ambos disfrutan de una mensualidad de ₡30.800!');
        $response->assertSee('Bebés y Niños Principiantes');
        $response->assertSee('Niños Grandes y Adolescentes');
        $response->assertSee('Adultos Principiantes');
        $response->assertSee('Open Pool');
        $response->assertSee('Aquafitness');
    }

    public function test_can_register_referral_simplified(): void
    {
        $payload = [
            'referrer' => [
                'full_name' => 'Marcos Jiménez Vega',
                'phone' => '88112233',
                'identification' => '115550666',
            ],
            'referee' => [
                'full_name' => 'Karla Montero Castro',
                'phone' => '89445566',
                'identification' => '117770888',
            ],
            'service_id' => $this->service->id,
            'notes' => 'Preferencia turno de la tarde',
            'terms' => '1',
        ];

        $response = $this->post(route('public.referral.store'), $payload);

        $referral = Referral::latest('id')->first();
        $this->assertNotNull($referral);
        $response->assertRedirect(route('public.referral.success', ['code' => $referral->referral_code]));

        $this->assertEquals('50688112233', $referral->referrer->phone);
        $this->assertEquals('50689445566', $referral->referee->phone);
        $this->assertEquals('Karla Montero Castro', $referral->student->full_name);
        $this->assertEquals('pending', $referral->status);
    }

    public function test_self_referral_is_strictly_blocked(): void
    {
        $payload = [
            'referrer' => [
                'full_name' => 'Roberto Solís',
                'phone' => '88990011',
            ],
            'referee' => [
                'full_name' => 'Roberto Solís Clon',
                'phone' => '88990011', // Mismo teléfono
            ],
            'service_id' => $this->service->id,
            'terms' => '1',
        ];

        $response = $this->post(route('public.referral.store'), $payload);
        $response->assertSessionHasErrors(['referee.phone']);
    }

    public function test_tracking_page_finds_record_by_code(): void
    {
        $referral = Referral::latest()->first();

        $response = $this->get(route('public.referral.track', ['q' => $referral->referral_code]));
        $response->assertStatus(200);
        $response->assertSee($referral->referral_code);
        $response->assertSee($referral->referrer->full_name);
        $response->assertSee($referral->referee->full_name);
    }

    public function test_admin_authentication_and_dashboard(): void
    {
        // Login exitoso
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin@aquatica.cr',
            'password' => 'AdminAquatica2026!',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);

        // Dashboard renders with KPIs
        $dashboardResponse = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard de Referidos');
        $dashboardResponse->assertSee('Total Referidos');
    }

    public function test_full_three_step_manual_verification_and_30800_benefit(): void
    {
        Storage::fake('public');

        // Crear expediente de prueba
        $referrer = Customer::create(['full_name' => 'Andrés Alpízar', 'phone' => '50681112222', 'customer_type' => 'both']);
        $referee = Customer::create(['full_name' => 'Beatriz Ulate', 'phone' => '50683334444', 'customer_type' => 'guardian']);
        $student = Student::create(['customer_id' => $referee->id, 'full_name' => 'Nicolás Alpízar', 'is_same_as_customer' => false]);

        $referral = Referral::create([
            'referral_code' => 'AQ-VERIF-' . uniqid(),
            'referrer_customer_id' => $referrer->id,
            'referee_customer_id' => $referee->id,
            'referee_student_id' => $student->id,
            'service_id' => $this->service->id,
            'status' => 'pending',
        ]);

        // PASO 1: Elegibilidad
        $step1 = $this->actingAs($this->adminUser)->post(route('admin.verify.eligibility', $referral), [
            'result' => 'approved',
            'observations' => 'Verificado que no tiene matrícula previa en el sistema.',
        ]);
        $step1->assertSessionHas('status');

        $referral->refresh();
        $this->assertEquals('eligible', $referral->status);
        $this->assertNotNull($referral->eligible_at);

        // PASO 2: Matrícula
        $step2 = $this->actingAs($this->adminUser)->post(route('admin.verify.enrollment', $referral), [
            'result' => 'approved',
            'observations' => 'Matriculado formalmente los sábados 10:00 AM.',
        ]);
        $step2->assertSessionHas('status');

        $referral->refresh();
        $this->assertEquals('enrolled', $referral->status);
        $this->assertNotNull($referral->enrolled_at);

        // PASO 3: Pago y Desbloqueo de ₡30.800
        $file = UploadedFile::fake()->image('comprobante_sinpe.png');
        $step3 = $this->actingAs($this->adminUser)->post(route('admin.verify.payment', $referral), [
            'result' => 'approved',
            'receipt_number' => 'SINPE-99887766',
            'payment_amount' => 30800.00,
            'apply_period' => '2026-11',
            'observations' => 'Comprobante validado en cuenta bancaria.',
            'proof_document' => $file,
        ]);
        $step3->assertSessionHas('status');

        $referral->refresh();
        $this->assertEquals('benefit_approved', $referral->status);
        $this->assertNotNull($referral->paid_at);
        $this->assertNotNull($referral->benefit_approved_at);

        // Verificar creación de 2 beneficios exactos de ₡30.800
        $this->assertCount(2, $referral->benefits);

        $referrerBenefit = $referral->benefits->where('beneficiary_type', 'referrer')->first();
        $refereeBenefit = $referral->benefits->where('beneficiary_type', 'referee')->first();

        $this->assertEquals(30800.00, (float) $referrerBenefit->discount_amount);
        $this->assertEquals(30800.00, (float) $refereeBenefit->discount_amount);
        $this->assertEquals('2026-11', $referrerBenefit->apply_to_period);
        $this->assertEquals($this->adminUser->id, $referrerBenefit->authorized_by);

        // Marcar beneficio como aplicado en cobro
        $applyResponse = $this->actingAs($this->adminUser)->post(route('admin.benefits.apply', $referrerBenefit), [
            'notes' => 'Aplicado en recibo 001',
        ]);
        $applyResponse->assertSessionHas('status');

        $referrerBenefit->refresh();
        $this->assertEquals('applied', $referrerBenefit->status);
    }

    public function test_csv_export_generates_valid_stream(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.exports.referrals'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
