<?php

namespace Database\Seeders\Messaging;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class MessagingTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping translations.');
            return;
        }

        $translations = [
            'navigation.messaging.sms_center' => ['en' => 'SMS Center', 'ar' => 'الرسائل النصية'],
            'navigation.messaging.sent_messages' => ['en' => 'Sent Messages', 'ar' => 'الرسائل المرسلة'],
            'navigation.messaging.contact_messages' => ['en' => 'Contact Us Messages', 'ar' => 'رسائل الاتصال بنا'],
            'navigation.messaging.templates_settings' => ['en' => 'Templates & Settings', 'ar' => 'القوالب والإعدادات'],

            'forms.message_contacts.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم'],
            'forms.message_contacts.phone' => ['en' => 'Phone', 'ar' => 'الرقم'],
            'forms.message_contacts.source' => ['en' => 'Source', 'ar' => 'المصدر'],

            'forms.sms_messages.recipients' => ['en' => 'Recipients', 'ar' => 'أرقام المستلمين (مثال: 995...,669...)'],
            'forms.sms_messages.message' => ['en' => 'Message', 'ar' => 'الرسالة'],

            'forms.contact_messages.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.contact_messages.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'forms.contact_messages.email' => ['en' => 'Email', 'ar' => 'البريد'],
            'forms.contact_messages.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'forms.contact_messages.message' => ['en' => 'Message', 'ar' => 'الرسالة'],

            'forms.sms_templates.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'forms.sms_templates.body_ar' => ['en' => 'Body (Arabic)', 'ar' => 'النص (عربي)'],
            'forms.sms_templates.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'forms.sms_templates.body_en' => ['en' => 'Body (English)', 'ar' => 'النص (إنجليزي)'],
            'forms.sms_templates.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            'forms.sms_settings.current_balance' => ['en' => 'Current Balance', 'ar' => 'الرصيد الحالي'],
            'forms.sms_settings.sender_name' => ['en' => 'Sender Name', 'ar' => 'اسم المرسل'],
            'forms.sms_settings.daily_limit' => ['en' => 'Daily Limit', 'ar' => 'الحد اليومي'],
            'forms.sms_settings.is_sending_enabled' => ['en' => 'Enable Sending', 'ar' => 'تفعيل الإرسال'],

            'tables.message_contacts.name_ar' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.message_contacts.phone' => ['en' => 'Phone', 'ar' => 'الرقم'],

            'tables.sms_messages.id' => ['en' => 'ID', 'ar' => 'الرقم'],
            'tables.sms_messages.created_at' => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],
            'tables.sms_messages.created_by' => ['en' => 'Sender', 'ar' => 'المستخدم/المرسل'],
            'tables.sms_messages.recipients_count' => ['en' => 'Recipients Count', 'ar' => 'عدد المستلمين'],
            'tables.sms_messages.message' => ['en' => 'Message', 'ar' => 'نص الرسالة'],
            'tables.sms_messages.status' => ['en' => 'Status', 'ar' => 'الحالة'],

            'tables.contact_messages.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.contact_messages.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'tables.contact_messages.email' => ['en' => 'Email', 'ar' => 'البريد'],
            'tables.contact_messages.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'tables.contact_messages.message' => ['en' => 'Message', 'ar' => 'الرسالة'],
            'tables.contact_messages.is_read' => ['en' => 'Read', 'ar' => 'مقروء'],

            'tables.sms_templates.name_ar' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.sms_templates.body_ar' => ['en' => 'Body', 'ar' => 'النص'],
            'tables.sms_templates.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            'actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'actions.use' => ['en' => 'Use', 'ar' => 'استخدام'],
            'actions.send' => ['en' => 'Send', 'ar' => 'إرسال'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.excel' => ['en' => 'Excel', 'ar' => 'Excel'],
            'actions.copy' => ['en' => 'Copy', 'ar' => 'نسخ'],
            'actions.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'actions.import_customers' => ['en' => 'Import All Customer Numbers', 'ar' => 'إضافة جميع أرقام العملاء'],
            'actions.mark_read' => ['en' => 'Mark as Read', 'ar' => 'تحديد كمقروء'],
            'actions.mark_unread' => ['en' => 'Mark as Unread', 'ar' => 'تحديد كغير مقروء'],

            'status.queued' => ['en' => 'Queued', 'ar' => 'قيد الإرسال'],
            'status.sent' => ['en' => 'Sent', 'ar' => 'تم الإرسال'],
            'status.failed' => ['en' => 'Failed', 'ar' => 'فشل'],

            'notifications.sms_sent_success' => ['en' => 'SMS sent successfully', 'ar' => 'تم إرسال الرسالة بنجاح'],
            'notifications.sms_sent_failed' => ['en' => 'Failed to send SMS', 'ar' => 'فشل إرسال الرسالة'],
            'notifications.contacts_imported' => ['en' => 'Contacts imported successfully', 'ar' => 'تم استيراد جهات الاتصال بنجاح'],
            'notifications.contact_created' => ['en' => 'Contact created successfully', 'ar' => 'تم إنشاء جهة الاتصال بنجاح'],
            'notifications.contact_updated' => ['en' => 'Contact updated successfully', 'ar' => 'تم تحديث جهة الاتصال بنجاح'],
            'notifications.contact_deleted' => ['en' => 'Contact deleted successfully', 'ar' => 'تم حذف جهة الاتصال بنجاح'],

            'widgets.sent_count' => ['en' => 'Sent Messages', 'ar' => 'الرسائل المرسلة'],
            'widgets.current_balance' => ['en' => 'Current Balance', 'ar' => 'الرصيد الحالي'],

            'pages.sms_center.title' => ['en' => 'SMS Center', 'ar' => 'الرسائل النصية'],
            'pages.sms_center.contacts_list' => ['en' => 'Contacts List', 'ar' => 'قائمة الأرقام'],
            'pages.sms_center.send_section' => ['en' => 'Send Message', 'ar' => 'إرسال رسالة'],

            'pages.sent_messages.title' => ['en' => 'Sent Messages', 'ar' => 'الرسائل المرسلة'],
            'pages.contact_messages.title' => ['en' => 'Contact Us Messages', 'ar' => 'رسائل الاتصال بنا'],
            'pages.templates.title' => ['en' => 'SMS Templates', 'ar' => 'قوالب الرسائل'],
            'pages.settings.title' => ['en' => 'SMS Settings', 'ar' => 'إعدادات الرسائل'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            $resultEn = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $english->id,
                ],
                [
                    'value' => $values['en'],
                ]
            );

            $resultAr = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );

            if ($resultEn->wasRecentlyCreated || $resultAr->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Messaging translations seeded: {$created} created, {$updated} updated.");
    }
}
