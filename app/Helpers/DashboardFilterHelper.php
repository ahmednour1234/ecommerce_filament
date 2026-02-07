<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;

class DashboardFilterHelper
{
    public static function getDefaultFilters(): array
    {
        return [
            'date_from' => now()->startOfMonth(),
            'date_to' => now()->endOfMonth(),
            'branch_id' => null,
            'transaction_type' => 'all',
            'revenue_type_id' => null,
            'expense_type_id' => null,
            'finance_type_id' => null,
            'currency_id' => null,
            'order_status' => null,
        ];
    }

    public static function parseFiltersFromRequest(): array
    {
        $request = request();
        $defaults = self::getDefaultFilters();

        $filters = [
            'date_from' => $request->get('date_from') 
                ? Carbon::parse($request->get('date_from'))->startOfDay() 
                : $defaults['date_from'],
            'date_to' => $request->get('date_to') 
                ? Carbon::parse($request->get('date_to'))->endOfDay() 
                : $defaults['date_to'],
            'branch_id' => $request->get('branch_id') ? (int) $request->get('branch_id') : null,
            'transaction_type' => $request->get('transaction_type', 'all'),
            'revenue_type_id' => $request->get('revenue_type_id') ? (int) $request->get('revenue_type_id') : null,
            'expense_type_id' => $request->get('expense_type_id') ? (int) $request->get('expense_type_id') : null,
            'finance_type_id' => $request->get('finance_type_id') ? (int) $request->get('finance_type_id') : null,
            'currency_id' => $request->get('currency_id') ? (int) $request->get('currency_id') : null,
            'order_status' => $request->get('order_status') ?: null,
        ];

        return self::validateDateRange($filters);
    }

    public static function validateDateRange(array $filters): array
    {
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $from = $filters['date_from'] instanceof Carbon 
                ? $filters['date_from'] 
                : Carbon::parse($filters['date_from']);
            $to = $filters['date_to'] instanceof Carbon 
                ? $filters['date_to'] 
                : Carbon::parse($filters['date_to']);

            if ($from->gt($to)) {
                $defaults = self::getDefaultFilters();
                $filters['date_from'] = $defaults['date_from'];
                $filters['date_to'] = $defaults['date_to'];
            }
        }

        return $filters;
    }

    public static function buildFilterQueryString(array $filters): string
    {
        $params = [];

        if (isset($filters['date_from']) && $filters['date_from']) {
            $params['date_from'] = $filters['date_from'] instanceof Carbon 
                ? $filters['date_from']->format('Y-m-d') 
                : $filters['date_from'];
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $params['date_to'] = $filters['date_to'] instanceof Carbon 
                ? $filters['date_to']->format('Y-m-d') 
                : $filters['date_to'];
        }

        if (isset($filters['branch_id']) && $filters['branch_id']) {
            $params['branch_id'] = $filters['branch_id'];
        }

        if (isset($filters['transaction_type']) && $filters['transaction_type'] !== 'all') {
            $params['transaction_type'] = $filters['transaction_type'];
        }

        if (isset($filters['revenue_type_id']) && $filters['revenue_type_id']) {
            $params['revenue_type_id'] = $filters['revenue_type_id'];
        }

        if (isset($filters['expense_type_id']) && $filters['expense_type_id']) {
            $params['expense_type_id'] = $filters['expense_type_id'];
        }

        if (isset($filters['finance_type_id']) && $filters['finance_type_id']) {
            $params['finance_type_id'] = $filters['finance_type_id'];
        }

        if (isset($filters['currency_id']) && $filters['currency_id']) {
            $params['currency_id'] = $filters['currency_id'];
        }

        if (isset($filters['order_status']) && $filters['order_status']) {
            $params['order_status'] = $filters['order_status'];
        }

        return http_build_query($params);
    }
}
