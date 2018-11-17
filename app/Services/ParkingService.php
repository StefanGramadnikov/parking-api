<?php namespace App\Services;

use App\Vehicle;
use Carbon\Carbon;
use App\VehicleCategory;
use Carbon\CarbonPeriod;
use App\Constants\ShiftHours;
use Illuminate\Support\Facades\DB;
use App\Constants\GeneralConstants;

class ParkingService
{
    /**
     * @return int|mixed
     */
    public function getAvailableSpaces()
    {
        //Get the total amount of taken parking spaces
        $parkingSpacesTaken = DB::table('vehicles')
            ->join('vehicle_categories', 'vehicles.vehicle_category_id', '=', 'vehicle_categories.id')
            ->sum('vehicle_categories.spaces_required');

        return GeneralConstants::PARKING_SPACES - $parkingSpacesTaken;
    }

    /**
     * @param VehicleCategory $category
     *
     * @return bool
     */
    public function checkIfSpaceIsAvailable(VehicleCategory $category)
    {
        if ($this->getAvailableSpaces() - $category->spaces_required >= 0) {
            return true;
        }

        return false;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return float|int
     */
    public function getAmountForStay(Vehicle $vehicle)
    {
        //Registered at and Left at Dates
        $registeredAt = $vehicle->created_at;
        $leftAt = Carbon::now();


        //Create period for day hours
        $dayParkedPeriod = new CarbonPeriod($registeredAt->toDateTimeString(), '1 hour', $leftAt->toDateTimeString());

        $dayHoursFilter = function ($date) {
            $dayStarts = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.ShiftHours::DAY_SHIFT_START);
            $dayEnds = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.ShiftHours::DAY_SHIFT_END);

            //Check if we match the day hours
            if ($date >= $dayStarts && $date < $dayEnds) {
                return true;
            }

            return false;
        };

        CarbonPeriod::macro('calculateParkingDayHours', function () use ($dayHoursFilter) {
            return $this->filter($dayHoursFilter)->count();
        });

        //Create period for night hours
        $nightParkedPeriod = new CarbonPeriod($registeredAt->toDateTimeString(), '1 hour', $leftAt->toDateTimeString());

        $nightHourFilter = function ($date) {
            $nightEnds = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.ShiftHours::DAY_SHIFT_START);
            $nightStarts = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d').' '.ShiftHours::DAY_SHIFT_END);

            //Check if we match the night hours
            if ($date < $nightEnds || $date >= $nightStarts) {
                return true;
            }

            return false;
        };

        CarbonPeriod::macro('calculateParkingNightHours', function () use ($nightHourFilter) {
            return $this->filter($nightHourFilter)->count();
        });

        //Calculate All Hours
        $dayHours = $dayParkedPeriod->calculateParkingDayHours();
        $nightHours = $nightParkedPeriod->calculateParkingNightHours();

        //Calculate amount
        $totalAmount = $dayHours * $vehicle->vehicleCategory->day_tariff;
        $totalAmount += $nightHours * $vehicle->vehicleCategory->night_tariff;

        //Subtract discount from total amount if there is a discount card provided
        if ($vehicle->discountCard) {
            $totalAmount = round($totalAmount - ($totalAmount * ($vehicle->discountCard->discount / 100)), 1);
        }

        return $totalAmount;
    }
}
