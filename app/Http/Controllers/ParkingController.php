<?php namespace App\Http\Controllers;

use App\Vehicle;
use App\DiscountCard;
use App\VehicleCategory;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Services\ParkingService;
use App\Constants\VehicleCategory as CategoryConstants;
use App\Constants\DiscountCard as DiscountCardConstants;

class ParkingController extends Controller
{
    /**
     *
     * Returns the total amount of available spaces
     *
     * @param ParkingService $parkingService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSpaces(ParkingService $parkingService)
    {
        $availableSpaces = $parkingService->getAvailableSpaces();

        return response()->json([
            'status'          => Status::SUCCESS,
            'availableSpaces' => $availableSpaces,
        ]);
    }

    /**
     *
     * Returns the due amount for the vehicle with the given ID
     *
     * @param Request $request
     * @param ParkingService $parkingService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDueAmount(Request $request, ParkingService $parkingService)
    {
        $data = $request->validate([
            'vehicleId' => 'required|exists:vehicles,vehicle_id',
        ], ['exists' => 'The vehicle with the given ID is not in the parking lot']);

        $vehicle = Vehicle::where('vehicle_id', $data['vehicleId'])->first();
        $totalAmount = $parkingService->getAmountForStay($vehicle);

        return response()->json([
            'status'  => Status::SUCCESS,
            'price'   => $totalAmount,
            'message' => 'The vehicle due amount is: '.$totalAmount,
        ]);
    }

    /**
     * @param Request $request
     * @param ParkingService $parkingService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerVehicle(Request $request, ParkingService $parkingService)
    {
        $data = $request->validate([
            'category'     => 'required|in:'.CategoryConstants::getValuesForValidation(),
            'discountCard' => 'in:'.DiscountCardConstants::getValuesForValidation(),
            'vehicleId'    => 'required|unique:vehicles,vehicle_id',
        ]);

        //Get vehicle category
        $vehicleCategory = VehicleCategory::where('slug', $data['category'])->first();

        //Check if there enough space available on the parking lot for the current vehicle
        if (!$parkingService->checkIfSpaceIsAvailable($vehicleCategory)) {
            return response()->json([
                'status'  => Status::ERROR,
                'message' => 'No spaces available in the parking lot',
            ], 422);
        }

        //If a discount card is provided in the request fetch it from the DB, so it can be associated
        $discountCard = array_key_exists('discountCard', $data) ? DiscountCard::where('slug', $data['discountCard'])->first() : null;

        //Create and save the vehicle
        $parkedVehicle = new Vehicle();
        $parkedVehicle->vehicleCategory()->associate($vehicleCategory);
        $parkedVehicle->discountCard()->associate($discountCard);
        $parkedVehicle->vehicle_id = $data['vehicleId'];

        $parkedVehicle->save();

        return response()->json([
            'status'  => Status::SUCCESS,
            'message' => 'Vehicle parked successfully',
        ]);
    }

    public function unRegisterVehicle(Request $request, ParkingService $parkingService)
    {
        $data = $request->validate([
            'vehicleId' => 'required|exists:vehicles,vehicle_id',
        ], ['exists' => 'The vehicle with the given ID is not in the parking lot']);

        $vehicle = Vehicle::where('vehicle_id', $data['vehicleId'])->first();
        $totalAmount = $parkingService->getAmountForStay($vehicle);

        $vehicle->delete();

        return response()->json([
            'status'  => Status::SUCCESS,
            'price'   => $totalAmount,
            'message' => 'Vehicle has left the parking lot successfully',
        ]);
    }
}
