<?php

namespace Tests\Feature;

use App\Models\BenefitHistory;
use App\Models\Customer;
use App\Models\Referral;
use App\Models\Service;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1DatabaseTest extends TestCase
{
    public function test_services_are_seeded_correctly(): void
    {
        $this->assertDatabaseCount('services', 5);

        $services = Service::where('is_active', true)->pluck('name')->toArray();

        $this->assertContains('Bebés y Niños Principiantes', $services);
        $this->assertContains('Niños Grandes y Adolescentes', $services);
        $this->assertContains('Adultos Principiantes', $services);
        $this->assertContains('Open Pool', $services);
        $this->assertContains('Aquafitness', $services);
    }

    public function test_customer_and_student_relation(): void
    {
        $customer = Customer::create([
            'full_name' => 'Carlos Morales Solano',
            'identification' => '112340567',
            'phone' => '88887777',
            'email' => 'carlos.morales@example.com',
            'customer_type' => 'guardian',
        ]);

        $student = Student::create([
            'customer_id' => $customer->id,
            'full_name' => 'Mateo Morales',
            'birth_date' => '2020-05-15',
            'gender' => 'male',
            'is_same_as_customer' => false,
        ]);

        $this->assertEquals('50688887777', $customer->normalized_phone);
        $this->assertCount(1, $customer->students);
        $this->assertEquals($customer->id, $student->customer->id);
    }

    public function test_referral_flow_and_benefit_generation(): void
    {
        $referrer = Customer::create([
            'full_name' => 'Laura Salazar Jiménez',
            'phone' => '83332211',
            'email' => 'laura@example.com',
            'customer_type' => 'adult_student',
        ]);

        $referee = Customer::create([
            'full_name' => 'Valeria Mora Castro',
            'phone' => '89994433',
            'email' => 'valeria@example.com',
            'customer_type' => 'guardian',
        ]);

        $student = Student::create([
            'customer_id' => $referee->id,
            'full_name' => 'Camila Mora',
            'birth_date' => '2019-02-10',
            'gender' => 'female',
        ]);

        $service = Service::where('slug', 'bebes-y-ninos-principiantes')->first();

        $referral = Referral::create([
            'referral_code' => 'AQ-REF-TEST-' . uniqid(),
            'referrer_customer_id' => $referrer->id,
            'referee_customer_id' => $referee->id,
            'referee_student_id' => $student->id,
            'service_id' => $service->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('Nuevo Registrado', $referral->status_label);
        $this->assertEquals($referrer->id, $referral->referrer->id);
        $this->assertEquals($referee->id, $referral->referee->id);
        $this->assertEquals($student->id, $referral->student->id);

        // Simular otorgamiento de beneficio de ₡30.800
        $benefitReferrer = BenefitHistory::create([
            'referral_id' => $referral->id,
            'beneficiary_customer_id' => $referrer->id,
            'beneficiary_type' => 'referrer',
            'discount_amount' => 30800.00,
            'status' => 'granted',
        ]);

        $benefitReferee = BenefitHistory::create([
            'referral_id' => $referral->id,
            'beneficiary_customer_id' => $referee->id,
            'beneficiary_type' => 'referee',
            'discount_amount' => 30800.00,
            'status' => 'granted',
        ]);

        $this->assertEquals(30800.00, (float) $benefitReferrer->discount_amount);
        $this->assertEquals(30800.00, (float) $benefitReferee->discount_amount);
        $this->assertCount(2, $referral->benefits);
    }

    public function test_system_settings_helper(): void
    {
        $amount = SystemSetting::get('referral_benefit_amount');
        $this->assertEquals(30800.00, (float) $amount);

        SystemSetting::set('custom_test_key', true, 'boolean', 'Clave de prueba');
        $this->assertTrue(SystemSetting::get('custom_test_key'));
    }
}
