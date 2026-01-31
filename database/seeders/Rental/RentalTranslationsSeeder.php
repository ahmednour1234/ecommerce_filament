<?php

namespace Database\Seeders\Rental;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class RentalTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Creating Rental module translations...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('⚠ English or Arabic language not found. Skipping Rental translations.');
            return;
        }

        $translations = [];

        $this->command->info('Step 1: Creating navigation translations...');
        $translations = array_merge($translations, [
            'sidebar.rental' => ['en' => 'Rental', 'ar' => 'قسم التأجير'],
            'navigation.rental' => ['en' => 'Rental Packages', 'ar' => 'باقات التأجير'],
            'navigation.rental_contracts' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'navigation.rental_requests' => ['en' => 'Rental Requests', 'ar' => 'طلبات التأجير'],
            'navigation.rental_cancel_refund' => ['en' => 'Cancel/Refund Requests', 'ar' => 'طلبات الإلغاء/الاسترجاع'],
            'navigation.rental_returned' => ['en' => 'Returned Contracts', 'ar' => 'العقود المسترجعة'],
            'navigation.rental_archived' => ['en' => 'Archived Contracts', 'ar' => 'العقود المؤرشفة'],
            'navigation.rental_reports' => ['en' => 'Rental Reports', 'ar' => 'تقارير التأجير'],
        ]);

        $this->command->info('Step 2: Creating contracts form translations...');
        $translations = array_merge($translations, [
            'rental.contracts.title' => ['en' => 'Rental Contracts', 'ar' => 'عقود التأجير'],
            'rental.contracts.create' => ['en' => 'Create Contract', 'ar' => 'إنشاء عقد'],
            'rental.contracts.edit' => ['en' => 'Edit Contract', 'ar' => 'تعديل عقد'],
            'rental.contracts.view' => ['en' => 'View Contract', 'ar' => 'عرض العقد'],
            'rental.fields.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'rental.fields.request_no' => ['en' => 'Request No', 'ar' => 'رقم الطلب'],
            'rental.fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'rental.fields.customer' => ['en' => 'Customer', 'ar' => 'العميل'],
            'rental.fields.worker' => ['en' => 'Worker', 'ar' => 'العامل/العاملة'],
            'rental.fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'rental.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'rental.fields.package' => ['en' => 'Package', 'ar' => 'الباقة'],
            'rental.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'rental.fields.payment_status' => ['en' => 'Payment Status', 'ar' => 'حالة الدفع'],
            'rental.fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البدء'],
            'rental.fields.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ الانتهاء'],
            'rental.fields.duration_type' => ['en' => 'Duration Type', 'ar' => 'نوع المدة'],
            'rental.fields.duration' => ['en' => 'Duration', 'ar' => 'المدة'],
            'rental.fields.tax_percent' => ['en' => 'Tax %', 'ar' => 'الضريبة %'],
            'rental.fields.discount_type' => ['en' => 'Discount Type', 'ar' => 'نوع الخصم'],
            'rental.fields.discount_value' => ['en' => 'Discount Value', 'ar' => 'قيمة الخصم'],
            'rental.fields.subtotal' => ['en' => 'Subtotal', 'ar' => 'المجموع الفرعي'],
            'rental.fields.tax_value' => ['en' => 'Tax Value', 'ar' => 'قيمة الضريبة'],
            'rental.fields.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'rental.fields.paid_total' => ['en' => 'Paid Total', 'ar' => 'المدفوع'],
            'rental.fields.remaining_total' => ['en' => 'Remaining Total', 'ar' => 'المتبقي'],
            'rental.fields.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'rental.status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'rental.status.suspended' => ['en' => 'Suspended', 'ar' => 'معلق'],
            'rental.status.completed' => ['en' => 'Completed', 'ar' => 'مكتمل'],
            'rental.status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغي'],
            'rental.status.returned' => ['en' => 'Returned', 'ar' => 'مسترجعة'],
            'rental.status.archived' => ['en' => 'Archived', 'ar' => 'مؤرشفة'],
            'rental.payment_status.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'rental.payment_status.unpaid' => ['en' => 'Unpaid', 'ar' => 'غير مدفوع'],
            'rental.payment_status.partial' => ['en' => 'Partial', 'ar' => 'جزئي'],
            'rental.payment_status.refunded' => ['en' => 'Refunded', 'ar' => 'مسترد'],
            'rental.discount_type.none' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'rental.discount_type.percent' => ['en' => 'Percent', 'ar' => 'نسبة مئوية'],
            'rental.discount_type.fixed' => ['en' => 'Fixed', 'ar' => 'مبلغ ثابت'],
            'rental.duration_type.day' => ['en' => 'Day', 'ar' => 'يوم'],
            'rental.duration_type.month' => ['en' => 'Month', 'ar' => 'شهر'],
            'rental.duration_type.year' => ['en' => 'Year', 'ar' => 'سنة'],
        ]);

        $this->command->info('Step 3: Creating requests translations...');
        $translations = array_merge($translations, [
            'rental.requests.title' => ['en' => 'Rental Requests', 'ar' => 'طلبات التأجير'],
            'rental.requests.create' => ['en' => 'Create Request', 'ar' => 'إنشاء طلب'],
            'rental.requests.view' => ['en' => 'View Request', 'ar' => 'عرض الطلب'],
            'rental.requests.convert' => ['en' => 'Convert to Contract', 'ar' => 'تحويل إلى عقد'],
            'rental.requests.approve' => ['en' => 'Approve', 'ar' => 'موافقة'],
            'rental.requests.reject' => ['en' => 'Reject', 'ar' => 'رفض'],
            'rental.requests.status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'rental.requests.status.under_review' => ['en' => 'Under Review', 'ar' => 'قيد المراجعة'],
            'rental.requests.status.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'rental.requests.status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'rental.requests.status.converted' => ['en' => 'Converted', 'ar' => 'محول'],
        ]);

        $this->command->info('Step 4: Creating cancel/refund translations...');
        $translations = array_merge($translations, [
            'rental.cancel_refund.title' => ['en' => 'Cancel/Refund Requests', 'ar' => 'طلبات الإلغاء/الاسترجاع'],
            'rental.cancel_refund.create' => ['en' => 'Create Request', 'ar' => 'إنشاء طلب'],
            'rental.cancel_refund.type.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'rental.cancel_refund.type.refund' => ['en' => 'Refund', 'ar' => 'استرجاع'],
            'rental.cancel_refund.reason' => ['en' => 'Reason', 'ar' => 'السبب'],
            'rental.cancel_refund.refund_amount' => ['en' => 'Refund Amount', 'ar' => 'مبلغ الاسترجاع'],
        ]);

        $this->command->info('Step 5: Creating payments translations...');
        $translations = array_merge($translations, [
            'rental.payments.title' => ['en' => 'Payments', 'ar' => 'المدفوعات'],
            'rental.payments.add' => ['en' => 'Add Payment', 'ar' => 'إضافة دفعة'],
            'rental.payments.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'rental.payments.paid_at' => ['en' => 'Paid At', 'ar' => 'تاريخ الدفع'],
            'rental.payments.method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'rental.payments.reference' => ['en' => 'Reference', 'ar' => 'المرجع'],
        ]);

        $this->command->info('Step 6: Creating print translations...');
        $translations = array_merge($translations, [
            'rental.print.contract' => ['en' => 'Print Contract', 'ar' => 'طباعة العقد'],
            'rental.print.invoice' => ['en' => 'Print Invoice', 'ar' => 'طباعة الفاتورة'],
        ]);

        $this->command->info('Step 7: Creating reports translations...');
        $translations = array_merge($translations, [
            'rental.reports.title' => ['en' => 'Rental Reports', 'ar' => 'تقارير التأجير'],
            'rental.reports.active_contracts' => ['en' => 'Active Contracts', 'ar' => 'العقود النشطة'],
            'rental.reports.dues_receivables' => ['en' => 'Dues/Receivables', 'ar' => 'المستحقات/الذمم'],
            'rental.reports.revenue' => ['en' => 'Revenue', 'ar' => 'الإيرادات'],
            'rental.reports.contracts' => ['en' => 'Contracts Report', 'ar' => 'تقرير العقود'],
            'rental.reports.worker_performance' => ['en' => 'Worker Performance', 'ar' => 'أداء العمال'],
            'rental.reports.customers' => ['en' => 'Customers Report', 'ar' => 'تقرير العملاء'],
            'rental.reports.cancellation_refund' => ['en' => 'Cancellation/Refund', 'ar' => 'الإلغاء/الاسترجاع'],
            'rental.reports.payments' => ['en' => 'Payments Report', 'ar' => 'تقرير المدفوعات'],
        ]);

        $this->command->info('Step 8: Creating actions translations...');
        $translations = array_merge($translations, [
            'rental.actions.renew' => ['en' => 'Renew', 'ar' => 'تجديد'],
            'rental.actions.archive' => ['en' => 'Archive', 'ar' => 'أرشفة'],
            'rental.actions.unarchive' => ['en' => 'Unarchive', 'ar' => 'إلغاء الأرشفة'],
            'rental.actions.update_status' => ['en' => 'Update Status', 'ar' => 'تحديث الحالة'],
            'rental.actions.sync_finance' => ['en' => 'Sync with Finance', 'ar' => 'مزامنة مع المالية'],
            'rental.actions.calculate' => ['en' => 'Calculate', 'ar' => 'حساب القيمة'],
        ]);

        $this->command->info('Step 9: Saving translations to database...');
        $created = 0;

        foreach ($translations as $key => $values) {
            if (isset($values['en'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $english->id,
                    ],
                    ['value' => $values['en']]
                );
                $created++;
            }

            if (isset($values['ar'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $arabic->id,
                    ],
                    ['value' => $values['ar']]
                );
                $created++;
            }
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info("✓ Rental translations created: {$created} entries");
        $this->command->info('═══════════════════════════════════════════════════════');
    }
}
