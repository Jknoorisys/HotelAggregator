<?php

namespace App\Http\Controllers\api\agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function App\Helpers\validateAgent;

class QuotationController extends Controller
{
    public function history(Request $request) {
        $validator = Validator::make($request->all(), [
            'agent_id'  => ['required','alpha_dash', Rule::notIn('undefined')],
            'page'      => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        } 

        try {
            $agent = validateAgent($request->agent_id);
            if (!empty($agent) && $agent->status == 'inactive') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.detail.inactive'),
                ], 400);
            }

            $page = $request->input(key: 'page', default: 1);
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $startDate = $request->input('from', '');
            $endDate = $request->input('to', '');

            $query = $agent->quotations();

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            $quotations = $query->orderBy('id', 'desc')
                            ->limit($limit)
                            ->offset($offset)
                            ->get();
    
            $total = $query->count();

            if (!empty($quotations)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.success'),
                    'data'      => $quotations,
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.list.failed'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
