<?php

namespace Tests\Unit;

use App\Helpers\OrderHelper;
use Carbon\Carbon;
use Tests\TestCase;

class OrderHelperTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset Carbon time mock after each test
        parent::tearDown();
    }

    public function test_is_order_day_active_with_empty_config()
    {
        config(['app.order_days' => '']);
        $this->assertTrue(OrderHelper::isOrderDayActive());

        config(['app.order_days' => '*']);
        $this->assertTrue(OrderHelper::isOrderDayActive());

        config(['app.order_days' => 'all']);
        $this->assertTrue(OrderHelper::isOrderDayActive());
    }

    public function test_is_order_day_active_with_day_names()
    {
        config(['app.order_days' => 'Monday,Wednesday,Friday']);

        // Monday
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00')); // Monday
        $this->assertTrue(OrderHelper::isOrderDayActive());

        // Tuesday
        Carbon::setTestNow(Carbon::parse('2026-06-02 10:00:00')); // Tuesday
        $this->assertFalse(OrderHelper::isOrderDayActive());

        // Wednesday
        Carbon::setTestNow(Carbon::parse('2026-06-03 10:00:00')); // Wednesday
        $this->assertTrue(OrderHelper::isOrderDayActive());
    }

    public function test_is_order_day_active_with_shorthand_names()
    {
        config(['app.order_days' => 'mon,tue,wed,thu,fri']);

        // Friday
        Carbon::setTestNow(Carbon::parse('2026-06-05 10:00:00')); // Friday
        $this->assertTrue(OrderHelper::isOrderDayActive());

        // Saturday
        Carbon::setTestNow(Carbon::parse('2026-06-06 10:00:00')); // Saturday
        $this->assertFalse(OrderHelper::isOrderDayActive());
    }

    public function test_is_order_day_active_with_indonesian_names()
    {
        config(['app.order_days' => 'Senin,Kamis,Sabtu']);

        // Monday (Senin)
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00'));
        $this->assertTrue(OrderHelper::isOrderDayActive());

        // Tuesday (Selasa)
        Carbon::setTestNow(Carbon::parse('2026-06-02 10:00:00'));
        $this->assertFalse(OrderHelper::isOrderDayActive());

        // Saturday (Sabtu)
        Carbon::setTestNow(Carbon::parse('2026-06-06 10:00:00'));
        $this->assertTrue(OrderHelper::isOrderDayActive());
    }

    public function test_is_order_day_active_with_numbers()
    {
        config(['app.order_days' => '1,3,5']); // Mon, Wed, Fri

        // Monday
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00'));
        $this->assertTrue(OrderHelper::isOrderDayActive());

        // Tuesday
        Carbon::setTestNow(Carbon::parse('2026-06-02 10:00:00'));
        $this->assertFalse(OrderHelper::isOrderDayActive());
    }

    public function test_get_active_days_formatted()
    {
        config(['app.order_days' => 'Monday,Tuesday,Wednesday,Thursday,Friday']);
        $this->assertEquals('Senin - Jumat', OrderHelper::getActiveDaysFormatted());

        config(['app.order_days' => 'mon,tue,wed,thu,fri,sat,sun']);
        $this->assertEquals('Setiap Hari', OrderHelper::getActiveDaysFormatted());

        config(['app.order_days' => 'Senin,Rabu,Jumat']);
        $this->assertEquals('Senin, Rabu & Jumat', OrderHelper::getActiveDaysFormatted());

        config(['app.order_days' => 'Sabtu']);
        $this->assertEquals('Sabtu', OrderHelper::getActiveDaysFormatted());
    }

    public function test_is_order_time_active_combines_hours_and_days()
    {
        config([
            'app.order_days' => 'Monday,Tuesday,Wednesday,Thursday,Friday',
            'app.order_hours.start' => '07:30',
            'app.order_hours.end' => '15:30',
        ]);

        // Monday at 10:00 (Open)
        Carbon::setTestNow(Carbon::parse('2026-06-01 10:00:00'));
        $this->assertTrue(OrderHelper::isOrderTimeActive());

        // Monday at 06:00 (Closed by hour)
        Carbon::setTestNow(Carbon::parse('2026-06-01 06:00:00'));
        $this->assertFalse(OrderHelper::isOrderTimeActive());

        // Saturday at 10:00 (Closed by day)
        Carbon::setTestNow(Carbon::parse('2026-06-06 10:00:00'));
        $this->assertFalse(OrderHelper::isOrderTimeActive());
    }
}
