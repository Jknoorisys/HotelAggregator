<?php

namespace App\Http\Controllers\api\hotel_beds;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function calculateSignature()
    {
        // Retrieve the public key, private key, and UTC date from your Laravel environment or configuration.
        $publicKey = config('hotelbeds.hotel.Api-key');
        $privateKey = config('hotelbeds.hotel.secret');
        $utcDate = time(); // Current UTC timestamp in seconds

        // Combine the public key, private key, and UTC date as in the JavaScript code.
        $assemble = $publicKey . $privateKey . $utcDate;

        // Calculate SHA-256 hash using HMAC
        $assemble = $publicKey . $privateKey . $utcDate;

        // Calculate the SHA-256 hash
        $hash = hash('sha256', $assemble);

        // Set the X-Signature in your response headers
        return $hash;
    }
    
    public function hotels(Request $request) {

        $validator = Validator::make($request->all(), [
            'stay'        => 'required|json',
            'occupancies' => 'required|json',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [
                "stay" => json_decode($request->stay, TRUE),
                "occupancies" => json_decode($request->occupancies, TRUE),
            ];        
    
            $hotel = $request->hotel ? $request->hotel : "";
            if (!empty($hotel) && isset($hotel)) {
                $data['hotels']['hotel'] = explode(',', $request->hotel);
            }

            $geolocation = $request->geolocation ? $request->geolocation : "";
            if (!empty($geolocation) && isset($geolocation)) {
                $data['geolocation']= json_decode($request->geolocation, TRUE);
            }

            $rooms = $request->rooms ? $request->rooms : "";
            if (!empty($rooms) && isset($rooms)) {
                $data['rooms']= json_decode($request->rooms, TRUE);
            }

            $filter = $request->filter ? $request->filter : "";
            if (!empty($filter) && isset($filter)) {
                $data['filter']= json_decode($request->filter, TRUE);
            }

            $boards = $request->boards ? $request->boards : "";
            if (!empty($boards) && isset($boards)) {
                $data['boards']= json_decode($request->boards, TRUE);
            }
            
            $dailyRate = $request->dailyRate ? $request->dailyRate : "";
            if (!empty($dailyRate) && isset($dailyRate)) {
                $data['dailyRate']= $request->dailyRate;
            }

            $language = $request->language ? $request->language : "";
            if (!empty($language) && isset($language)) {
                $data['language']= $request->language;
            }

            $keywords = $request->keywords ? $request->keywords : "";
            if (!empty($keywords) && isset($keywords)) {
                $data['keywords']= json_decode($request->keywords, TRUE);
            }

            $review = $request->review ? $request->review : "";
            if (!empty($review) && isset($review)) {
                $data['reviews']= json_decode($request->review, TRUE);
            }

            $accommodations = $request->accommodations ? $request->accommodations : "";
            if (!empty($accommodations) && isset($accommodations)) {
                $data['accommodations']= explode(',', $request->accommodations);
            }

            $inclusions = $request->inclusions ? $request->inclusions : "";
            if (!empty($inclusions) && isset($inclusions)) {
                $data['inclusions']= explode(',', $request->inclusions);
            }
            
            $destination = $request->destination ? $request->destination : "";
            if (!empty($destination) && isset($destination)) {
                $data['destination'] = json_decode($request->destination, TRUE);
            }
            
            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->post(config('hotelbeds.end-point').'/hotel-api/1.0/hotels', $data);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function checkrates(Request $request) {

        $validator = Validator::make($request->all(), [
            'rooms'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [
                "rooms" => json_decode($request->rooms, TRUE),
            ]; 

            $language = $request->language ? $request->language : "";
            if (!empty($language) && isset($language)) {
                $data['language']= $request->language;
            }
            
            $upselling = $request->upselling ? $request->upselling : "";
            if (!empty($upselling) && isset($upselling)) {
                $data['upselling']= $request->upselling;
            }

            $expandCXL = $request->expandCXL ? $request->expandCXL : "";
            if (!empty($expandCXL) && isset($expandCXL)) {
                $data['expandCXL']= $request->expandCXL;
            }

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->post(config('hotelbeds.end-point').'/hotel-api/1.0/checkrates', $data);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function bookings(Request $request) {

        $validator = Validator::make($request->all(), [
            'holder'  => 'required|json',
            'rooms'   => 'required|json',
            'clientReference' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [
                "holder" => json_decode($request->holder, TRUE),
                "rooms" => json_decode($request->rooms, TRUE),
                'clientReference' => $request->clientReference,
            ];

            $paymentData = $request->paymentData ? $request->paymentData : "";
            if (!empty($paymentData) && isset($paymentData)) {
                $data['paymentData']= json_decode($request->paymentData, TRUE);
            }

            $creationUser = $request->creationUser ? $request->creationUser : "";
            if (!empty($creationUser) && isset($creationUser)) {
                $data['creationUser']= $request->creationUser;
            }

            $remark = $request->remark ? $request->remark : "";
            if (!empty($remark) && isset($remark)) {
                $data['remark']= $request->remark;
            }
            
            $voucher = $request->voucher ? $request->voucher : "";
            if (!empty($voucher) && isset($voucher)) {
                $data['voucher']= json_decode($request->voucher, TRUE);
            }

            $language = $request->language ? $request->language : "";
            if (!empty($language) && isset($language)) {
                $data['language']= $request->language;
            }

            $tolerance = $request->tolerance ? $request->tolerance : "";
            if (!empty($tolerance) && isset($tolerance)) {
                $data['tolerance']= $request->tolerance;
            }

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->post(config('hotelbeds.secure-end-point').'/hotel-api/1.0/bookings', $data);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.booking.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.booking.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function bookingList(Request $request) {

        $validator = Validator::make($request->all(), [
            'from'  => 'required|numeric',
            'to'   => 'required|numeric',
            'start'  => 'required|date|date_format:Y-m-d',
            'end'   => 'required|date|date_format:Y-m-d',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [
                "from" => $request->from,
                "to" => $request->to,
                "start" => $request->start,
                "end" => $request->end,
            ];

            $filterType = $request->filterType ? $request->filterType : "";
            if (!empty($filterType) && isset($filterType)) {
                $data['filterType']= $request->filterType;
            }

            $status = $request->status ? $request->status : "";
            if (!empty($status) && isset($status)) {
                $data['status']= $request->status;
            }

            $remark = $request->remark ? $request->remark : "";
            if (!empty($remark) && isset($remark)) {
                $data['remark']= $request->remark;
            }
            
            $clientReference = $request->clientReference ? $request->clientReference : "";
            if (!empty($clientReference) && isset($clientReference)) {
                $data['clientReference']= $request->clientReference;
            }

            $creationUser = $request->creationUser ? $request->creationUser : "";
            if (!empty($creationUser) && isset($creationUser)) {
                $data['creationUser']= $request->creationUser;
            }

            $country = $request->country ? $request->country : "";
            if (!empty($country) && isset($country)) {
                $data['country']= $request->country;
            }

            $hotel = $request->hotel ? $request->hotel : "";
            if (!empty($hotel) && isset($hotel)) {
                $data['hotel'] = $request->hotel;
            }

            $destination = $request->destination ? $request->destination : "";
            if (!empty($destination) && isset($destination)) {
                $data['destination'] = $request->destination;
            }
            
            $queryString = http_build_query($data);

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->get(config('hotelbeds.end-point').'/hotel-api/1.0/bookings?'. $queryString);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function bookingDetails(Request $request) {

        $validator = Validator::make($request->all(), [
            'bookingId'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [];

            $language = $request->language ? $request->language : "";
            if (!empty($language) && isset($language)) {
                $data['language']= $request->language;
            }

            $bookingId = $request->bookingId;

            $queryString = http_build_query($data);

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->get(config('hotelbeds.end-point').'/hotel-api/1.0/bookings/'.$bookingId.'?', $queryString);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function bookingChange(Request $request) {

        $validator = Validator::make($request->all(), [
            'bookingId'  => 'required',
            'mode'       => 'required',
            'booking'    => 'required|json'
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [
                'mode' => $request->mode,
                'booking' => json_decode($request->booking, TRUE)
            ];

            $language = $request->language ? $request->language : "";
            if (!empty($language) && isset($language)) {
                $data['language']= $request->language;
            }

            $paymentData = $request->paymentData ? $request->paymentData : "";
            if (!empty($paymentData) && isset($paymentData)) {
                $data['paymentData']= json_decode($request->paymentData, TRUE);
            }

            $bookingId = $request->bookingId;

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->put(config('hotelbeds.secure-end-point').'/hotel-api/1.0/bookings/'.$bookingId, $data);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.booking.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.booking.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function bookingCancel(Request $request) {

        $validator = Validator::make($request->all(), [
            'bookingId'  => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [];

            $cancellationFlag = $request->cancellationFlag ? $request->cancellationFlag : "";
            if (!empty($cancellationFlag) && isset($cancellationFlag)) {
                $data['cancellationFlag']= $request->cancellationFlag;
            }

            $language = $request->language ? $request->language : "";
            if (!empty($language) && isset($language)) {
                $data['language']= $request->language;
            }

            $bookingId = $request->bookingId;

            $queryString = http_build_query($data);

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->delete(config('hotelbeds.end-point').'/hotel-api/1.0/bookings/'.$bookingId.'?'. $queryString);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.booking-cancel.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.booking-cancel.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }

    public function bookingReconfirmation(Request $request) {

        $validator = Validator::make($request->all(), [
            'from'  => 'required|numeric',
            'to'    => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors()
            ],400);
        }

        try {
            
            $data = [
                'from' => $request->from,
                'to'   => $request->to,
            ];

            $start = $request->start ? $request->start : "";
            if (!empty($start) && isset($start)) {
                $data['start']= $request->start;
            }

            $end = $request->end ? $request->end : "";
            if (!empty($end) && isset($end)) {
                $data['end']= $request->end;
            }

            $filterType = $request->filterType ? $request->filterType : "";
            if (!empty($filterType) && isset($filterType)) {
                $data['filterType']= $request->filterType;
            }

            $references = $request->references ? $request->references : "";
            if (!empty($references) && isset($references)) {
                $data['references']= explode(',', $request->references);
            }

            $clientReferences = $request->clientReferences ? $request->clientReferences : "";
            if (!empty($clientReferences) && isset($clientReferences)) {
                $data['clientReferences'] = explode(',', $request->clientReferences);
            }

            $Signature = self::calculateSignature();

            $response = Http::withHeaders([
                'Api-key' => config('hotelbeds.hotel.Api-key'),
                'X-Signature' => $Signature,
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip',
                'Content-Type' => 'application/json',
            ])->get(config('hotelbeds.end-point').'/hotel-api/1.0/reconfirmations', $data);
            
            $responseData = $response->json();
            
            $status = $response->status();

            if ($status == "200") {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $responseData
                ],$status);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                    'data'      => $responseData
                ],$status);
            }
            
        } catch (\Throwable $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.error'),
                'error'     => $e->getMessage()
            ],500);
        }
    }
}
