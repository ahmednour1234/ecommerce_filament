<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class DashboardTranslationSeeder extends Seeder
{
    public function run(): void
    {
        // Dashboard translation keys
        $translations = [
            // Navigation
            'navigation.dashboard' => [
                'en' => 'Dashboard',
                'zh' => '仪表板',
                'es' => 'Panel',
                'ar' => 'لوحة التحكم',
                'hi' => 'डैशबोर्ड',
                'pt' => 'Painel',
                'ru' => 'Панель управления',
                'ja' => 'ダッシュボード',
                'de' => 'Dashboard',
                'fr' => 'Tableau de bord',
            ],
            'navigation.users' => [
                'en' => 'Users',
                'zh' => '用户',
                'es' => 'Usuarios',
                'ar' => 'المستخدمون',
                'hi' => 'उपयोगकर्ता',
                'pt' => 'Usuários',
                'ru' => 'Пользователи',
                'ja' => 'ユーザー',
                'de' => 'Benutzer',
                'fr' => 'Utilisateurs',
            ],
            'navigation.roles' => [
                'en' => 'Roles',
                'zh' => '角色',
                'es' => 'Roles',
                'ar' => 'الأدوار',
                'hi' => 'भूमिकाएं',
                'pt' => 'Funções',
                'ru' => 'Роли',
                'ja' => '役割',
                'de' => 'Rollen',
                'fr' => 'Rôles',
            ],
            'navigation.permissions' => [
                'en' => 'Permissions',
                'zh' => '权限',
                'es' => 'Permisos',
                'ar' => 'الصلاحيات',
                'hi' => 'अनुमतियां',
                'pt' => 'Permissões',
                'ru' => 'Разрешения',
                'ja' => '権限',
                'de' => 'Berechtigungen',
                'fr' => 'Autorisations',
            ],
            'navigation.maincore' => [
                'en' => 'MainCore',
                'zh' => '核心',
                'es' => 'Núcleo Principal',
                'ar' => 'النواة الرئيسية',
                'hi' => 'मुख्य कोर',
                'pt' => 'Núcleo Principal',
                'ru' => 'Основное ядро',
                'ja' => 'メインコア',
                'de' => 'Hauptkern',
                'fr' => 'Noyau Principal',
            ],
            'navigation.system' => [
                'en' => 'System',
                'zh' => '系统',
                'es' => 'Sistema',
                'ar' => 'النظام',
                'hi' => 'सिस्टम',
                'pt' => 'Sistema',
                'ru' => 'Система',
                'ja' => 'システム',
                'de' => 'System',
                'fr' => 'Système',
            ],
            'navigation.my_profile' => [
                'en' => 'My Profile',
                'zh' => '我的资料',
                'es' => 'Mi Perfil',
                'ar' => 'ملفي الشخصي',
                'hi' => 'मेरी प्रोफ़ाइल',
                'pt' => 'Meu Perfil',
                'ru' => 'Мой профиль',
                'ja' => 'マイプロフィール',
                'de' => 'Mein Profil',
                'fr' => 'Mon Profil',
            ],
            'navigation.system_settings' => [
                'en' => 'System Settings',
                'zh' => '系统设置',
                'es' => 'Configuración del Sistema',
                'ar' => 'إعدادات النظام',
                'hi' => 'सिस्टम सेटिंग्स',
                'pt' => 'Configurações do Sistema',
                'ru' => 'Настройки системы',
                'ja' => 'システム設定',
                'de' => 'Systemeinstellungen',
                'fr' => 'Paramètres Système',
            ],
            // Model translations
            'navigation.user' => [
                'en' => 'Users',
                'zh' => '用户',
                'es' => 'Usuarios',
                'ar' => 'المستخدمون',
                'hi' => 'उपयोगकर्ता',
                'pt' => 'Usuários',
                'ru' => 'Пользователи',
                'ja' => 'ユーザー',
                'de' => 'Benutzer',
                'fr' => 'Utilisateurs',
            ],
            'navigation.role' => [
                'en' => 'Roles',
                'zh' => '角色',
                'es' => 'Roles',
                'ar' => 'الأدوار',
                'hi' => 'भूमिकाएं',
                'pt' => 'Funções',
                'ru' => 'Роли',
                'ja' => '役割',
                'de' => 'Rollen',
                'fr' => 'Rôles',
            ],
            'navigation.permission' => [
                'en' => 'Permissions',
                'zh' => '权限',
                'es' => 'Permisos',
                'ar' => 'الصلاحيات',
                'hi' => 'अनुमतियां',
                'pt' => 'Permissões',
                'ru' => 'Разрешения',
                'ja' => '権限',
                'de' => 'Berechtigungen',
                'fr' => 'Autorisations',
            ],
            'navigation.language' => [
                'en' => 'Languages',
                'zh' => '语言',
                'es' => 'Idiomas',
                'ar' => 'اللغات',
                'hi' => 'भाषाएं',
                'pt' => 'Idiomas',
                'ru' => 'Языки',
                'ja' => '言語',
                'de' => 'Sprachen',
                'fr' => 'Langues',
            ],
            'navigation.currency' => [
                'en' => 'Currencies',
                'zh' => '货币',
                'es' => 'Monedas',
                'ar' => 'العملات',
                'hi' => 'मुद्राएं',
                'pt' => 'Moedas',
                'ru' => 'Валюты',
                'ja' => '通貨',
                'de' => 'Währungen',
                'fr' => 'Devises',
            ],
            'navigation.currencyrate' => [
                'en' => 'Currency Rates',
                'zh' => '汇率',
                'es' => 'Tasas de Cambio',
                'ar' => 'أسعار الصرف',
                'hi' => 'मुद्रा दरें',
                'pt' => 'Taxas de Câmbio',
                'ru' => 'Курсы валют',
                'ja' => '為替レート',
                'de' => 'Wechselkurse',
                'fr' => 'Taux de Change',
            ],
            'navigation.setting' => [
                'en' => 'Settings',
                'zh' => '设置',
                'es' => 'Configuraciones',
                'ar' => 'الإعدادات',
                'hi' => 'सेटिंग्स',
                'pt' => 'Configurações',
                'ru' => 'Настройки',
                'ja' => '設定',
                'de' => 'Einstellungen',
                'fr' => 'Paramètres',
            ],
            'navigation.theme' => [
                'en' => 'Themes',
                'zh' => '主题',
                'es' => 'Temas',
                'ar' => 'السمات',
                'hi' => 'थीम',
                'pt' => 'Temas',
                'ru' => 'Темы',
                'ja' => 'テーマ',
                'de' => 'Themen',
                'fr' => 'Thèmes',
            ],
            'navigation.translation' => [
                'en' => 'Translations',
                'zh' => '翻译',
                'es' => 'Traducciones',
                'ar' => 'الترجمات',
                'hi' => 'अनुवाद',
                'pt' => 'Traduções',
                'ru' => 'Переводы',
                'ja' => '翻訳',
                'de' => 'Übersetzungen',
                'fr' => 'Traductions',
            ],
            'navigation.paymentprovider' => [
                'en' => 'Payment Providers',
                'zh' => '支付提供商',
                'es' => 'Proveedores de Pago',
                'ar' => 'مزودو الدفع',
                'hi' => 'भुगतान प्रदाता',
                'pt' => 'Provedores de Pagamento',
                'ru' => 'Провайдеры платежей',
                'ja' => '決済プロバイダー',
                'de' => 'Zahlungsanbieter',
                'fr' => 'Fournisseurs de Paiement',
            ],
            'navigation.paymentmethod' => [
                'en' => 'Payment Methods',
                'zh' => '支付方式',
                'es' => 'Métodos de Pago',
                'ar' => 'طرق الدفع',
                'hi' => 'भुगतान विधियां',
                'pt' => 'Métodos de Pagamento',
                'ru' => 'Способы оплаты',
                'ja' => '支払い方法',
                'de' => 'Zahlungsmethoden',
                'fr' => 'Méthodes de Paiement',
            ],
            'navigation.paymenttransaction' => [
                'en' => 'Payment Transactions',
                'zh' => '支付交易',
                'es' => 'Transacciones de Pago',
                'ar' => 'معاملات الدفع',
                'hi' => 'भुगतान लेनदेन',
                'pt' => 'Transações de Pagamento',
                'ru' => 'Платежные транзакции',
                'ja' => '決済取引',
                'de' => 'Zahlungstransaktionen',
                'fr' => 'Transactions de Paiement',
            ],
            'navigation.shippingprovider' => [
                'en' => 'Shipping Providers',
                'zh' => '运输提供商',
                'es' => 'Proveedores de Envío',
                'ar' => 'مزودو الشحن',
                'hi' => 'शिपिंग प्रदाता',
                'pt' => 'Provedores de Envio',
                'ru' => 'Провайдеры доставки',
                'ja' => '配送プロバイダー',
                'de' => 'Versandanbieter',
                'fr' => 'Fournisseurs d\'Expédition',
            ],
            'navigation.shipment' => [
                'en' => 'Shipments',
                'zh' => '货运',
                'es' => 'Envíos',
                'ar' => 'الشحنات',
                'hi' => 'शिपमेंट',
                'pt' => 'Envios',
                'ru' => 'Отправления',
                'ja' => '出荷',
                'de' => 'Sendungen',
                'fr' => 'Expéditions',
            ],
            'navigation.notificationchannel' => [
                'en' => 'Notification Channels',
                'zh' => '通知渠道',
                'es' => 'Canales de Notificación',
                'ar' => 'قنوات الإشعارات',
                'hi' => 'सूचना चैनल',
                'pt' => 'Canais de Notificação',
                'ru' => 'Каналы уведомлений',
                'ja' => '通知チャネル',
                'de' => 'Benachrichtigungskanäle',
                'fr' => 'Canaux de Notification',
            ],
            'navigation.notificationtemplate' => [
                'en' => 'Notification Templates',
                'zh' => '通知模板',
                'es' => 'Plantillas de Notificación',
                'ar' => 'قوالب الإشعارات',
                'hi' => 'सूचना टेम्प्लेट',
                'pt' => 'Modelos de Notificação',
                'ru' => 'Шаблоны уведомлений',
                'ja' => '通知テンプレート',
                'de' => 'Benachrichtigungsvorlagen',
                'fr' => 'Modèles de Notification',
            ],

            // Common Actions
            'actions.create' => [
                'en' => 'Create',
                'zh' => '创建',
                'es' => 'Crear',
                'ar' => 'إنشاء',
                'hi' => 'बनाएं',
                'pt' => 'Criar',
                'ru' => 'Создать',
                'ja' => '作成',
                'de' => 'Erstellen',
                'fr' => 'Créer',
            ],
            'actions.edit' => [
                'en' => 'Edit',
                'zh' => '编辑',
                'es' => 'Editar',
                'ar' => 'تعديل',
                'hi' => 'संपादित करें',
                'pt' => 'Editar',
                'ru' => 'Редактировать',
                'ja' => '編集',
                'de' => 'Bearbeiten',
                'fr' => 'Modifier',
            ],
            'actions.delete' => [
                'en' => 'Delete',
                'zh' => '删除',
                'es' => 'Eliminar',
                'ar' => 'حذف',
                'hi' => 'हटाएं',
                'pt' => 'Excluir',
                'ru' => 'Удалить',
                'ja' => '削除',
                'de' => 'Löschen',
                'fr' => 'Supprimer',
            ],
            'actions.save' => [
                'en' => 'Save',
                'zh' => '保存',
                'es' => 'Guardar',
                'ar' => 'حفظ',
                'hi' => 'सहेजें',
                'pt' => 'Salvar',
                'ru' => 'Сохранить',
                'ja' => '保存',
                'de' => 'Speichern',
                'fr' => 'Enregistrer',
            ],
            'actions.cancel' => [
                'en' => 'Cancel',
                'zh' => '取消',
                'es' => 'Cancelar',
                'ar' => 'إلغاء',
                'hi' => 'रद्द करें',
                'pt' => 'Cancelar',
                'ru' => 'Отмена',
                'ja' => 'キャンセル',
                'de' => 'Abbrechen',
                'fr' => 'Annuler',
            ],
            'actions.search' => [
                'en' => 'Search',
                'zh' => '搜索',
                'es' => 'Buscar',
                'ar' => 'بحث',
                'hi' => 'खोजें',
                'pt' => 'Pesquisar',
                'ru' => 'Поиск',
                'ja' => '検索',
                'de' => 'Suchen',
                'fr' => 'Rechercher',
            ],

            // Dashboard Messages
            'dashboard.welcome' => [
                'en' => 'Welcome to Dashboard',
                'zh' => '欢迎使用仪表板',
                'es' => 'Bienvenido al Panel',
                'ar' => 'مرحباً بك في لوحة التحكم',
                'hi' => 'डैशबोर्ड में आपका स्वागत है',
                'pt' => 'Bem-vindo ao Painel',
                'ru' => 'Добро пожаловать в панель управления',
                'ja' => 'ダッシュボードへようこそ',
                'de' => 'Willkommen im Dashboard',
                'fr' => 'Bienvenue sur le Tableau de bord',
            ],
            'dashboard.overview' => [
                'en' => 'Overview',
                'zh' => '概览',
                'es' => 'Resumen',
                'ar' => 'نظرة عامة',
                'hi' => 'अवलोकन',
                'pt' => 'Visão Geral',
                'ru' => 'Обзор',
                'ja' => '概要',
                'de' => 'Übersicht',
                'fr' => 'Aperçu',
            ],
            'dashboard.statistics' => [
                'en' => 'Statistics',
                'zh' => '统计',
                'es' => 'Estadísticas',
                'ar' => 'الإحصائيات',
                'hi' => 'आंकड़े',
                'pt' => 'Estatísticas',
                'ru' => 'Статистика',
                'ja' => '統計',
                'de' => 'Statistiken',
                'fr' => 'Statistiques',
            ],

            // Common Labels
            'labels.name' => [
                'en' => 'Name',
                'zh' => '名称',
                'es' => 'Nombre',
                'ar' => 'الاسم',
                'hi' => 'नाम',
                'pt' => 'Nome',
                'ru' => 'Имя',
                'ja' => '名前',
                'de' => 'Name',
                'fr' => 'Nom',
            ],
            'labels.email' => [
                'en' => 'Email',
                'zh' => '电子邮件',
                'es' => 'Correo',
                'ar' => 'البريد الإلكتروني',
                'hi' => 'ईमेल',
                'pt' => 'E-mail',
                'ru' => 'Электронная почта',
                'ja' => 'メール',
                'de' => 'E-Mail',
                'fr' => 'E-mail',
            ],
            'labels.status' => [
                'en' => 'Status',
                'zh' => '状态',
                'es' => 'Estado',
                'ar' => 'الحالة',
                'hi' => 'स्थिति',
                'pt' => 'Status',
                'ru' => 'Статус',
                'ja' => 'ステータス',
                'de' => 'Status',
                'fr' => 'Statut',
            ],
            'labels.active' => [
                'en' => 'Active',
                'zh' => '活跃',
                'es' => 'Activo',
                'ar' => 'نشط',
                'hi' => 'सक्रिय',
                'pt' => 'Ativo',
                'ru' => 'Активный',
                'ja' => 'アクティブ',
                'de' => 'Aktiv',
                'fr' => 'Actif',
            ],
            'labels.inactive' => [
                'en' => 'Inactive',
                'zh' => '非活跃',
                'es' => 'Inactivo',
                'ar' => 'غير نشط',
                'hi' => 'निष्क्रिय',
                'pt' => 'Inativo',
                'ru' => 'Неактивный',
                'ja' => '非アクティブ',
                'de' => 'Inaktiv',
                'fr' => 'Inactif',
            ],
            'labels.created_at' => [
                'en' => 'Created At',
                'zh' => '创建时间',
                'es' => 'Creado En',
                'ar' => 'تاريخ الإنشاء',
                'hi' => 'बनाया गया',
                'pt' => 'Criado Em',
                'ru' => 'Создано',
                'ja' => '作成日時',
                'de' => 'Erstellt am',
                'fr' => 'Créé le',
            ],
            'labels.updated_at' => [
                'en' => 'Updated At',
                'zh' => '更新时间',
                'es' => 'Actualizado En',
                'ar' => 'تاريخ التحديث',
                'hi' => 'अपडेट किया गया',
                'pt' => 'Atualizado Em',
                'ru' => 'Обновлено',
                'ja' => '更新日時',
                'de' => 'Aktualisiert am',
                'fr' => 'Mis à jour le',
            ],

            // Messages
            'messages.created' => [
                'en' => 'Record created successfully',
                'zh' => '记录创建成功',
                'es' => 'Registro creado exitosamente',
                'ar' => 'تم إنشاء السجل بنجاح',
                'hi' => 'रिकॉर्ड सफलतापूर्वक बनाया गया',
                'pt' => 'Registro criado com sucesso',
                'ru' => 'Запись успешно создана',
                'ja' => 'レコードが正常に作成されました',
                'de' => 'Datensatz erfolgreich erstellt',
                'fr' => 'Enregistrement créé avec succès',
            ],
            'messages.updated' => [
                'en' => 'Record updated successfully',
                'zh' => '记录更新成功',
                'es' => 'Registro actualizado exitosamente',
                'ar' => 'تم تحديث السجل بنجاح',
                'hi' => 'रिकॉर्ड सफलतापूर्वक अपडेट किया गया',
                'pt' => 'Registro atualizado com sucesso',
                'ru' => 'Запись успешно обновлена',
                'ja' => 'レコードが正常に更新されました',
                'de' => 'Datensatz erfolgreich aktualisiert',
                'fr' => 'Enregistrement mis à jour avec succès',
            ],
            'messages.deleted' => [
                'en' => 'Record deleted successfully',
                'zh' => '记录删除成功',
                'es' => 'Registro eliminado exitosamente',
                'ar' => 'تم حذف السجل بنجاح',
                'hi' => 'रिकॉर्ड सफलतापूर्वक हटाया गया',
                'pt' => 'Registro excluído com sucesso',
                'ru' => 'Запись успешно удалена',
                'ja' => 'レコードが正常に削除されました',
                'de' => 'Datensatz erfolgreich gelöscht',
                'fr' => 'Enregistrement supprimé avec succès',
            ],

            // Dashboard Statistics
            'dashboard.stats.total_assets' => [
                'en' => 'Total Assets',
                'ar' => 'إجمالي الأصول',
            ],
            'dashboard.stats.total_assets_description' => [
                'en' => 'Sum of all asset accounts',
                'ar' => 'مجموع جميع حسابات الأصول',
            ],
            'dashboard.stats.total_liabilities' => [
                'en' => 'Total Liabilities',
                'ar' => 'إجمالي الخصوم',
            ],
            'dashboard.stats.total_liabilities_description' => [
                'en' => 'Sum of all liability accounts',
                'ar' => 'مجموع جميع حسابات الخصوم',
            ],
            'dashboard.stats.total_equity' => [
                'en' => 'Total Equity',
                'ar' => 'إجمالي حقوق الملكية',
            ],
            'dashboard.stats.total_equity_description' => [
                'en' => 'Sum of all equity accounts',
                'ar' => 'مجموع جميع حسابات حقوق الملكية',
            ],
            'dashboard.stats.total_products' => [
                'en' => 'Total Products',
                'ar' => 'إجمالي المنتجات',
            ],
            'dashboard.stats.total_products_description' => [
                'en' => 'active products',
                'ar' => 'منتجات نشطة',
            ],
            'dashboard.stats.total_categories' => [
                'en' => 'Total Categories',
                'ar' => 'إجمالي الفئات',
            ],
            'dashboard.stats.total_categories_description' => [
                'en' => 'Active categories',
                'ar' => 'فئات نشطة',
            ],
            'dashboard.stats.total_brands' => [
                'en' => 'Total Brands',
                'ar' => 'إجمالي العلامات التجارية',
            ],
            'dashboard.stats.total_brands_description' => [
                'en' => 'Active brands',
                'ar' => 'علامات تجارية نشطة',
            ],
            'dashboard.stats.low_stock_alert' => [
                'en' => 'Low Stock Alert',
                'ar' => 'تنبيه المخزون المنخفض',
            ],
            'dashboard.stats.low_stock_alert_description' => [
                'en' => 'Products with stock ≤ 10',
                'ar' => 'المنتجات ذات المخزون ≤ 10',
            ],
            'dashboard.stats.inventory_value' => [
                'en' => 'Inventory Value',
                'ar' => 'قيمة المخزون',
            ],
            'dashboard.stats.inventory_value_description' => [
                'en' => 'Total product inventory value',
                'ar' => 'إجمالي قيمة مخزون المنتجات',
            ],
            'dashboard.stats.todays_orders' => [
                'en' => 'Today\'s Orders',
                'ar' => 'طلبات اليوم',
            ],
            'dashboard.stats.todays_orders_description' => [
                'en' => 'Orders placed today',
                'ar' => 'الطلبات المقدمة اليوم',
            ],
            'dashboard.stats.this_month_orders' => [
                'en' => 'This Month Orders',
                'ar' => 'طلبات هذا الشهر',
            ],
            'dashboard.stats.this_month_orders_description' => [
                'en' => 'Orders this month',
                'ar' => 'الطلبات هذا الشهر',
            ],
            'dashboard.stats.pending_orders' => [
                'en' => 'Pending Orders',
                'ar' => 'الطلبات المعلقة',
            ],
            'dashboard.stats.pending_orders_description' => [
                'en' => 'completed, cancelled',
                'ar' => 'مكتملة، ملغاة',
            ],
            'dashboard.stats.todays_revenue' => [
                'en' => 'Today\'s Revenue',
                'ar' => 'إيرادات اليوم',
            ],
            'dashboard.stats.todays_revenue_description' => [
                'en' => 'From completed orders',
                'ar' => 'من الطلبات المكتملة',
            ],
            'dashboard.stats.monthly_revenue' => [
                'en' => 'Monthly Revenue',
                'ar' => 'الإيرادات الشهرية',
            ],
            'dashboard.stats.monthly_revenue_description' => [
                'en' => 'This month\'s total',
                'ar' => 'إجمالي هذا الشهر',
            ],
            'dashboard.stats.average_order_value' => [
                'en' => 'Average Order Value',
                'ar' => 'متوسط قيمة الطلب',
            ],
            'dashboard.stats.average_order_value_description' => [
                'en' => 'From completed orders',
                'ar' => 'من الطلبات المكتملة',
            ],
            'dashboard.stats.total_customers' => [
                'en' => 'Total Customers',
                'ar' => 'إجمالي العملاء',
            ],
            'dashboard.stats.total_customers_description' => [
                'en' => 'Active customers',
                'ar' => 'عملاء نشطون',
            ],
            'dashboard.stats.total_orders' => [
                'en' => 'Total Orders',
                'ar' => 'إجمالي الطلبات',
            ],
            'dashboard.stats.total_orders_description' => [
                'en' => 'pending, completed',
                'ar' => 'معلقة، مكتملة',
            ],
            'dashboard.stats.total_revenue' => [
                'en' => 'Total Revenue',
                'ar' => 'إجمالي الإيرادات',
            ],
            'dashboard.stats.total_revenue_description' => [
                'en' => 'From paid invoices',
                'ar' => 'من الفواتير المدفوعة',
            ],
            'dashboard.stats.total_invoices' => [
                'en' => 'Total Invoices',
                'ar' => 'إجمالي الفواتير',
            ],
            'dashboard.stats.total_invoices_description' => [
                'en' => 'All invoices',
                'ar' => 'جميع الفواتير',
            ],
            'dashboard.stats.orders_over_time' => [
                'en' => 'Orders Over Time',
                'ar' => 'الطلبات عبر الزمن',
            ],
            'dashboard.stats.invoices_payments' => [
                'en' => 'Invoices & Payments',
                'ar' => 'الفواتير والمدفوعات',
            ],
            'dashboard.stats.orders_by_status' => [
                'en' => 'Orders by Status',
                'ar' => 'الطلبات حسب الحالة',
            ],
            
            // Status words
            'dashboard.stats.status.completed' => [
                'en' => 'completed',
                'ar' => 'مكتملة',
            ],
            'dashboard.stats.status.cancelled' => [
                'en' => 'cancelled',
                'ar' => 'ملغاة',
            ],
            'dashboard.stats.status.pending' => [
                'en' => 'pending',
                'ar' => 'معلقة',
            ],

            // Notification Channels - Forms
            'forms.notification_channels.type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'forms.notification_channels.type.options.email' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'forms.notification_channels.type.options.sms' => [
                'en' => 'SMS',
                'ar' => 'رسالة نصية',
            ],
            'forms.notification_channels.type.options.push' => [
                'en' => 'Push Notification',
                'ar' => 'إشعار فوري',
            ],
            'forms.notification_channels.type.options.slack' => [
                'en' => 'Slack',
                'ar' => 'Slack',
            ],
            'forms.notification_channels.type.options.webhook' => [
                'en' => 'Webhook',
                'ar' => 'Webhook',
            ],
            'forms.notification_channels.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.notification_channels.config.label' => [
                'en' => 'Configuration',
                'ar' => 'الإعدادات',
            ],
            'forms.notification_channels.is_active.label' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],

            // Notification Channels - Tables
            'tables.notification_channels.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.notification_channels.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.notification_channels.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'tables.notification_channels.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.notification_channels.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.notification_channels.filters.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.notification_channels.filters.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.notification_channels.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.notification_channels.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Notification Channels - Pages
            'pages.notifications.channels.title' => [
                'en' => 'Notification Channels',
                'ar' => 'قنوات الإشعارات',
            ],
            'pages.notifications.channels.create.title' => [
                'en' => 'Create Notification Channel',
                'ar' => 'إضافة قناة إشعار',
            ],
            'pages.notifications.channels.edit.title' => [
                'en' => 'Edit Notification Channel',
                'ar' => 'تعديل قناة إشعار',
            ],

            // Notification Templates - Forms
            'forms.notification_templates.key.label' => [
                'en' => 'Key',
                'ar' => 'المفتاح',
            ],
            'forms.notification_templates.channel_id.label' => [
                'en' => 'Channel',
                'ar' => 'القناة',
            ],
            'forms.notification_templates.language_id.label' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],
            'forms.notification_templates.subject.label' => [
                'en' => 'Subject',
                'ar' => 'الموضوع',
            ],
            'forms.notification_templates.body_text.label' => [
                'en' => 'Body Text',
                'ar' => 'نص الرسالة',
            ],
            'forms.notification_templates.body_html.label' => [
                'en' => 'Body HTML',
                'ar' => 'محتوى HTML',
            ],
            'forms.notification_templates.variables.label' => [
                'en' => 'Variables',
                'ar' => 'المتغيرات',
            ],
            'forms.notification_templates.is_active.label' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],

            // Notification Templates - Tables
            'tables.notification_templates.key' => [
                'en' => 'Key',
                'ar' => 'المفتاح',
            ],
            'tables.notification_templates.channel' => [
                'en' => 'Channel',
                'ar' => 'القناة',
            ],
            'tables.notification_templates.language' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],
            'tables.notification_templates.subject' => [
                'en' => 'Subject',
                'ar' => 'الموضوع',
            ],
            'tables.notification_templates.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'tables.notification_templates.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.notification_templates.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.notification_templates.filters.channel' => [
                'en' => 'Channel',
                'ar' => 'القناة',
            ],
            'tables.notification_templates.filters.language' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],
            'tables.notification_templates.filters.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.notification_templates.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.notification_templates.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Notification Templates - Pages
            'pages.notifications.templates.title' => [
                'en' => 'Notification Templates',
                'ar' => 'قوالب الإشعارات',
            ],
            'pages.notifications.templates.create.title' => [
                'en' => 'Create Notification Template',
                'ar' => 'إضافة قالب إشعار',
            ],
            'pages.notifications.templates.edit.title' => [
                'en' => 'Edit Notification Template',
                'ar' => 'تعديل قالب إشعار',
            ],

            // Payment Methods - Forms
            'forms.payment_methods.provider_id.label' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'forms.payment_methods.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.payment_methods.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.payment_methods.fee_fixed.label' => [
                'en' => 'Fee Fixed',
                'ar' => 'الرسوم الثابتة',
            ],
            'forms.payment_methods.fee_percent.label' => [
                'en' => 'Fee Percent',
                'ar' => 'نسبة الرسوم',
            ],
            'forms.payment_methods.is_default.label' => [
                'en' => 'Is Default',
                'ar' => 'افتراضي',
            ],
            'forms.payment_methods.is_active.label' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'forms.payment_methods.display_order.label' => [
                'en' => 'Display Order',
                'ar' => 'ترتيب العرض',
            ],

            // Payment Methods - Tables
            'tables.payment_methods.provider' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'tables.payment_methods.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.payment_methods.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.payment_methods.fee_fixed' => [
                'en' => 'Fee Fixed',
                'ar' => 'الرسوم الثابتة',
            ],
            'tables.payment_methods.fee_percent' => [
                'en' => 'Fee Percent',
                'ar' => 'نسبة الرسوم',
            ],
            'tables.payment_methods.is_default' => [
                'en' => 'Is Default',
                'ar' => 'افتراضي',
            ],
            'tables.payment_methods.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'tables.payment_methods.display_order' => [
                'en' => 'Display Order',
                'ar' => 'ترتيب العرض',
            ],
            'tables.payment_methods.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.payment_methods.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.payment_methods.filters.provider' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'tables.payment_methods.filters.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.payment_methods.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.payment_methods.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],
            'tables.payment_methods.filters.is_default' => [
                'en' => 'Default',
                'ar' => 'افتراضي',
            ],
            'tables.payment_methods.filters.default_only' => [
                'en' => 'Default only',
                'ar' => 'الافتراضية فقط',
            ],
            'tables.payment_methods.filters.non_default_only' => [
                'en' => 'Non-default only',
                'ar' => 'غير الافتراضية فقط',
            ],

            // Payment Methods - Pages
            'pages.integrations.payment_methods.title' => [
                'en' => 'Payment Methods',
                'ar' => 'طرق الدفع',
            ],
            'pages.integrations.payment_methods.create.title' => [
                'en' => 'Create Payment Method',
                'ar' => 'إضافة طريقة دفع',
            ],
            'pages.integrations.payment_methods.edit.title' => [
                'en' => 'Edit Payment Method',
                'ar' => 'تعديل طريقة دفع',
            ],

            // Payment Providers - Forms
            'forms.payment_providers.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.payment_providers.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.payment_providers.driver.label' => [
                'en' => 'Driver',
                'ar' => 'السائق',
            ],
            'forms.payment_providers.config.label' => [
                'en' => 'Configuration',
                'ar' => 'الإعدادات',
            ],
            'forms.payment_providers.is_active.label' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],

            // Payment Providers - Tables
            'tables.payment_providers.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.payment_providers.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.payment_providers.driver' => [
                'en' => 'Driver',
                'ar' => 'السائق',
            ],
            'tables.payment_providers.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'tables.payment_providers.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.payment_providers.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.payment_providers.filters.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.payment_providers.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.payment_providers.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Payment Providers - Pages
            'pages.integrations.payment_providers.title' => [
                'en' => 'Payment Providers',
                'ar' => 'مزودو الدفع',
            ],
            'pages.integrations.payment_providers.create.title' => [
                'en' => 'Create Payment Provider',
                'ar' => 'إضافة مزود دفع',
            ],
            'pages.integrations.payment_providers.edit.title' => [
                'en' => 'Edit Payment Provider',
                'ar' => 'تعديل مزود دفع',
            ],

            // Payment Transactions - Forms
            'forms.payment_transactions.payable_type.label' => [
                'en' => 'Payable Type',
                'ar' => 'نوع المستحقات',
            ],
            'forms.payment_transactions.payable_type.options.order' => [
                'en' => 'Order',
                'ar' => 'طلب',
            ],
            'forms.payment_transactions.payable_type.options.invoice' => [
                'en' => 'Invoice',
                'ar' => 'فاتورة',
            ],
            'forms.payment_transactions.payable_id.label' => [
                'en' => 'Payable ID',
                'ar' => 'معرف المستحقات',
            ],
            'forms.payment_transactions.user_id.label' => [
                'en' => 'User',
                'ar' => 'المستخدم',
            ],
            'forms.payment_transactions.payment_method_id.label' => [
                'en' => 'Method',
                'ar' => 'الطريقة',
            ],
            'forms.payment_transactions.provider_id.label' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'forms.payment_transactions.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.payment_transactions.amount.label' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'forms.payment_transactions.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.payment_transactions.status.options.pending' => [
                'en' => 'Pending',
                'ar' => 'معلق',
            ],
            'forms.payment_transactions.status.options.processing' => [
                'en' => 'Processing',
                'ar' => 'قيد المعالجة',
            ],
            'forms.payment_transactions.status.options.completed' => [
                'en' => 'Completed',
                'ar' => 'مكتمل',
            ],
            'forms.payment_transactions.status.options.failed' => [
                'en' => 'Failed',
                'ar' => 'فشل',
            ],
            'forms.payment_transactions.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'forms.payment_transactions.status.options.refunded' => [
                'en' => 'Refunded',
                'ar' => 'مسترد',
            ],
            'forms.payment_transactions.provider_reference.label' => [
                'en' => 'Provider Reference',
                'ar' => 'مرجع المزود',
            ],
            'forms.payment_transactions.meta.label' => [
                'en' => 'Meta',
                'ar' => 'بيانات إضافية',
            ],
            'forms.payment_transactions.paid_at.label' => [
                'en' => 'Paid At',
                'ar' => 'تاريخ الدفع',
            ],

            // Payment Transactions - Tables
            'tables.payment_transactions.payable_type' => [
                'en' => 'Payable Type',
                'ar' => 'نوع المستحقات',
            ],
            'tables.payment_transactions.payable_id' => [
                'en' => 'Payable ID',
                'ar' => 'معرف المستحقات',
            ],
            'tables.payment_transactions.user' => [
                'en' => 'User',
                'ar' => 'المستخدم',
            ],
            'tables.payment_transactions.method' => [
                'en' => 'Method',
                'ar' => 'الطريقة',
            ],
            'tables.payment_transactions.provider' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'tables.payment_transactions.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'tables.payment_transactions.amount' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'tables.payment_transactions.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.payment_transactions.provider_reference' => [
                'en' => 'Provider Reference',
                'ar' => 'مرجع المزود',
            ],
            'tables.payment_transactions.paid_at' => [
                'en' => 'Paid At',
                'ar' => 'تاريخ الدفع',
            ],
            'tables.payment_transactions.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.payment_transactions.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.payment_transactions.filters.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.payment_transactions.filters.method' => [
                'en' => 'Method',
                'ar' => 'الطريقة',
            ],
            'tables.payment_transactions.filters.provider' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'tables.payment_transactions.filters.user' => [
                'en' => 'User',
                'ar' => 'المستخدم',
            ],

            // Payment Transactions - Pages
            'pages.integrations.payment_transactions.title' => [
                'en' => 'Payment Transactions',
                'ar' => 'معاملات الدفع',
            ],
            'pages.integrations.payment_transactions.create.title' => [
                'en' => 'Create Payment Transaction',
                'ar' => 'إضافة معاملة دفع',
            ],
            'pages.integrations.payment_transactions.edit.title' => [
                'en' => 'Edit Payment Transaction',
                'ar' => 'تعديل معاملة دفع',
            ],

            // Shipments - Forms
            'forms.shipments.shippable_type.label' => [
                'en' => 'Shippable Type',
                'ar' => 'نوع الشحنة',
            ],
            'forms.shipments.shippable_type.options.order' => [
                'en' => 'Order',
                'ar' => 'طلب',
            ],
            'forms.shipments.shippable_type.options.product' => [
                'en' => 'Product',
                'ar' => 'منتج',
            ],
            'forms.shipments.shippable_id.label' => [
                'en' => 'Shippable ID',
                'ar' => 'معرف الشحنة',
            ],
            'forms.shipments.shipping_provider_id.label' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'forms.shipments.tracking_number.label' => [
                'en' => 'Tracking Number',
                'ar' => 'رقم التتبع',
            ],
            'forms.shipments.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.shipments.status.options.pending' => [
                'en' => 'Pending',
                'ar' => 'معلق',
            ],
            'forms.shipments.status.options.processing' => [
                'en' => 'Processing',
                'ar' => 'قيد المعالجة',
            ],
            'forms.shipments.status.options.shipped' => [
                'en' => 'Shipped',
                'ar' => 'تم الشحن',
            ],
            'forms.shipments.status.options.delivered' => [
                'en' => 'Delivered',
                'ar' => 'تم التسليم',
            ],
            'forms.shipments.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'forms.shipments.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.shipments.price.label' => [
                'en' => 'Price',
                'ar' => 'السعر',
            ],
            'forms.shipments.meta.label' => [
                'en' => 'Meta',
                'ar' => 'بيانات إضافية',
            ],

            // Shipments - Tables
            'tables.shipments.shippable_type' => [
                'en' => 'Shippable Type',
                'ar' => 'نوع الشحنة',
            ],
            'tables.shipments.shippable_id' => [
                'en' => 'Shippable ID',
                'ar' => 'معرف الشحنة',
            ],
            'tables.shipments.provider' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],
            'tables.shipments.tracking_number' => [
                'en' => 'Tracking Number',
                'ar' => 'رقم التتبع',
            ],
            'tables.shipments.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.shipments.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'tables.shipments.price' => [
                'en' => 'Price',
                'ar' => 'السعر',
            ],
            'tables.shipments.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.shipments.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.shipments.filters.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.shipments.filters.provider' => [
                'en' => 'Provider',
                'ar' => 'المزود',
            ],

            // Shipments - Pages
            'pages.integrations.shipments.title' => [
                'en' => 'Shipments',
                'ar' => 'الشحنات',
            ],
            'pages.integrations.shipments.create.title' => [
                'en' => 'Create Shipment',
                'ar' => 'إضافة شحنة',
            ],
            'pages.integrations.shipments.edit.title' => [
                'en' => 'Edit Shipment',
                'ar' => 'تعديل شحنة',
            ],

            // Shipping Providers - Forms
            'forms.shipping_providers.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.shipping_providers.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.shipping_providers.config.label' => [
                'en' => 'Configuration',
                'ar' => 'الإعدادات',
            ],
            'forms.shipping_providers.is_active.label' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],

            // Shipping Providers - Tables
            'tables.shipping_providers.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.shipping_providers.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.shipping_providers.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'tables.shipping_providers.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.shipping_providers.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.shipping_providers.filters.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.shipping_providers.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.shipping_providers.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Shipping Providers - Pages
            'pages.integrations.shipping_providers.title' => [
                'en' => 'Shipping Providers',
                'ar' => 'مزودو الشحن',
            ],
            'pages.integrations.shipping_providers.create.title' => [
                'en' => 'Create Shipping Provider',
                'ar' => 'إضافة مزود شحن',
            ],
            'pages.integrations.shipping_providers.edit.title' => [
                'en' => 'Edit Shipping Provider',
                'ar' => 'تعديل مزود شحن',
            ],

            // Translations - Forms
            'forms.translations.key.label' => [
                'en' => 'Translation Key',
                'ar' => 'مفتاح الترجمة',
            ],
            'forms.translations.key.helper' => [
                'en' => 'e.g., dashboard.welcome, auth.login',
                'ar' => 'مثال: dashboard.welcome, auth.login',
            ],
            'forms.translations.group.label' => [
                'en' => 'Group',
                'ar' => 'المجموعة',
            ],
            'forms.translations.group.helper' => [
                'en' => 'Group name like: dashboard, auth, validation, etc.',
                'ar' => 'اسم المجموعة مثل: dashboard, auth, validation, إلخ',
            ],
            'forms.translations.language_id.label' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],
            'forms.translations.value.label' => [
                'en' => 'Translation Value',
                'ar' => 'قيمة الترجمة',
            ],

            // Translations - Tables
            'tables.translations.key' => [
                'en' => 'Key',
                'ar' => 'المفتاح',
            ],
            'tables.translations.group' => [
                'en' => 'Group',
                'ar' => 'المجموعة',
            ],
            'tables.translations.language' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],
            'tables.translations.value' => [
                'en' => 'Value',
                'ar' => 'القيمة',
            ],
            'tables.translations.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.translations.updated_at' => [
                'en' => 'Updated At',
                'ar' => 'تاريخ التحديث',
            ],
            'tables.translations.filters.group' => [
                'en' => 'Group',
                'ar' => 'المجموعة',
            ],
            'tables.translations.filters.group_options.dashboard' => [
                'en' => 'Dashboard',
                'ar' => 'لوحة التحكم',
            ],
            'tables.translations.filters.group_options.auth' => [
                'en' => 'Authentication',
                'ar' => 'المصادقة',
            ],
            'tables.translations.filters.group_options.validation' => [
                'en' => 'Validation',
                'ar' => 'التحقق',
            ],
            'tables.translations.filters.group_options.common' => [
                'en' => 'Common',
                'ar' => 'عام',
            ],
            'tables.translations.filters.language' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],

            // Branches - Forms
            'forms.branches.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.branches.code.helper' => [
                'en' => 'Unique code for the branch',
                'ar' => 'رمز فريد للفرع',
            ],
            'forms.branches.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.branches.parent_id.label' => [
                'en' => 'Parent Branch',
                'ar' => 'الفرع الرئيسي',
            ],
            'forms.branches.parent_id.helper' => [
                'en' => 'Optional: Select a parent branch if this is a sub-branch',
                'ar' => 'اختياري: اختر فرعًا رئيسيًا إذا كان هذا فرعًا فرعيًا',
            ],
            'forms.branches.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.branches.status.options.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.branches.status.options.inactive' => [
                'en' => 'Inactive',
                'ar' => 'غير نشط',
            ],
            'forms.branches.address.label' => [
                'en' => 'Address',
                'ar' => 'العنوان',
            ],
            'forms.branches.phone.label' => [
                'en' => 'Phone',
                'ar' => 'الهاتف',
            ],
            'forms.branches.email.label' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'forms.branches.metadata.label' => [
                'en' => 'Metadata',
                'ar' => 'البيانات الوصفية',
            ],
            'forms.branches.metadata.key_label' => [
                'en' => 'Key',
                'ar' => 'المفتاح',
            ],
            'forms.branches.metadata.value_label' => [
                'en' => 'Value',
                'ar' => 'القيمة',
            ],
            'forms.branches.metadata.helper' => [
                'en' => 'Additional flexible data (optional)',
                'ar' => 'بيانات إضافية مرنة (اختياري)',
            ],

            // Branches - Tables
            'tables.branches.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.branches.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.branches.parent_branch' => [
                'en' => 'Parent Branch',
                'ar' => 'الفرع الرئيسي',
            ],
            'tables.branches.phone' => [
                'en' => 'Phone',
                'ar' => 'الهاتف',
            ],
            'tables.branches.email' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'tables.branches.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.branches.users' => [
                'en' => 'Users',
                'ar' => 'المستخدمون',
            ],
            'tables.branches.filters.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.branches.filters.parent_branch' => [
                'en' => 'Parent Branch',
                'ar' => 'الفرع الرئيسي',
            ],

            // Languages - Forms
            'forms.languages.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.languages.name.label' => [
                'en' => 'Name (EN)',
                'ar' => 'الاسم (إنجليزي)',
            ],
            'forms.languages.native_name.label' => [
                'en' => 'Native Name',
                'ar' => 'الاسم الأصلي',
            ],
            'forms.languages.is_default.label' => [
                'en' => 'Default',
                'ar' => 'افتراضي',
            ],
            'forms.languages.is_default.helper' => [
                'en' => 'Only one language should be default.',
                'ar' => 'يجب أن تكون لغة واحدة فقط هي الافتراضية.',
            ],
            'forms.languages.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.languages.direction.label' => [
                'en' => 'Direction',
                'ar' => 'الاتجاه',
            ],
            'forms.languages.direction.options.ltr' => [
                'en' => 'Left to Right',
                'ar' => 'من اليسار إلى اليمين',
            ],
            'forms.languages.direction.options.rtl' => [
                'en' => 'Right to Left',
                'ar' => 'من اليمين إلى اليسار',
            ],

            // Languages - Tables
            'tables.languages.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.languages.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.languages.native_name' => [
                'en' => 'Native Name',
                'ar' => 'الاسم الأصلي',
            ],
            'tables.languages.is_default' => [
                'en' => 'Default',
                'ar' => 'افتراضي',
            ],
            'tables.languages.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.languages.direction' => [
                'en' => 'Dir',
                'ar' => 'الاتجاه',
            ],

            // Currency Rates - Forms
            'forms.currency_rates.base_currency_id.label' => [
                'en' => 'Base Currency',
                'ar' => 'العملة الأساسية',
            ],
            'forms.currency_rates.target_currency_id.label' => [
                'en' => 'Target Currency',
                'ar' => 'العملة المستهدفة',
            ],
            'forms.currency_rates.rate.label' => [
                'en' => 'Rate',
                'ar' => 'السعر',
            ],
            'forms.currency_rates.valid_from.label' => [
                'en' => 'Valid From',
                'ar' => 'صالح من',
            ],

            // Currency Rates - Tables
            'tables.currency_rates.base' => [
                'en' => 'Base',
                'ar' => 'الأساسية',
            ],
            'tables.currency_rates.target' => [
                'en' => 'Target',
                'ar' => 'المستهدفة',
            ],
            'tables.currency_rates.rate' => [
                'en' => 'Rate',
                'ar' => 'السعر',
            ],
            'tables.currency_rates.valid_from' => [
                'en' => 'Valid From',
                'ar' => 'صالح من',
            ],

            // Settings - Forms
            'forms.settings.key.label' => [
                'en' => 'Setting Key',
                'ar' => 'مفتاح الإعداد',
            ],
            'forms.settings.key.helper' => [
                'en' => 'Select a setting key. Common keys: app.name, app.url, etc.',
                'ar' => 'اختر مفتاح إعداد. المفاتيح الشائعة: app.name, app.url, إلخ',
            ],
            'forms.settings.group.label' => [
                'en' => 'Group',
                'ar' => 'المجموعة',
            ],
            'forms.settings.group.helper' => [
                'en' => 'Example: app, mail, payment, ui',
                'ar' => 'مثال: app, mail, payment, ui',
            ],
            'forms.settings.type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'forms.settings.type.options.string' => [
                'en' => 'String',
                'ar' => 'نص',
            ],
            'forms.settings.type.options.int' => [
                'en' => 'Integer',
                'ar' => 'عدد صحيح',
            ],
            'forms.settings.type.options.bool' => [
                'en' => 'Boolean',
                'ar' => 'منطقي',
            ],
            'forms.settings.type.options.array' => [
                'en' => 'Array/JSON',
                'ar' => 'مصفوفة/JSON',
            ],
            'forms.settings.value.label' => [
                'en' => 'Value',
                'ar' => 'القيمة',
            ],
            'forms.settings.value.helper' => [
                'en' => 'For JSON/array, use valid JSON.',
                'ar' => 'لـ JSON/مصفوفة، استخدم JSON صالح.',
            ],
            'forms.settings.is_public.label' => [
                'en' => 'Public',
                'ar' => 'عام',
            ],
            'forms.settings.autoload.label' => [
                'en' => 'Autoload',
                'ar' => 'تحميل تلقائي',
            ],

            // Settings - Tables
            'tables.settings.key' => [
                'en' => 'Key',
                'ar' => 'المفتاح',
            ],
            'tables.settings.group' => [
                'en' => 'Group',
                'ar' => 'المجموعة',
            ],
            'tables.settings.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.settings.is_public' => [
                'en' => 'Public',
                'ar' => 'عام',
            ],
            'tables.settings.autoload' => [
                'en' => 'Autoload',
                'ar' => 'تحميل تلقائي',
            ],

            // Warehouses - Forms
            'forms.warehouses.code.label' => [
                'en' => 'Warehouse Code',
                'ar' => 'رمز المستودع',
            ],
            'forms.warehouses.code.helper' => [
                'en' => 'Unique code for the warehouse (e.g., WH-MAIN)',
                'ar' => 'رمز فريد للمستودع (مثل: WH-MAIN)',
            ],
            'forms.warehouses.name.label' => [
                'en' => 'Warehouse Name',
                'ar' => 'اسم المستودع',
            ],
            'forms.warehouses.branch_id.label' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'forms.warehouses.is_active.label' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],

            // Warehouses - Tables
            'tables.warehouses.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.warehouses.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.warehouses.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'tables.warehouses.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            'tables.warehouses.products' => [
                'en' => 'Products',
                'ar' => 'المنتجات',
            ],
            'tables.warehouses.filters.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'tables.warehouses.filters.is_active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.warehouses.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.warehouses.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // System Settings Page
            'pages.settings.system_settings.title' => [
                'en' => 'System Settings',
                'ar' => 'إعدادات النظام',
            ],
            'pages.settings.system_settings.sections.general_settings' => [
                'en' => 'General Settings',
                'ar' => 'الإعدادات العامة',
            ],
            'pages.settings.system_settings.sections.general_settings.description' => [
                'en' => 'Configure basic application settings',
                'ar' => 'تكوين الإعدادات الأساسية للتطبيق',
            ],
            'pages.settings.system_settings.sections.localization' => [
                'en' => 'Localization',
                'ar' => 'الموقعية',
            ],
            'pages.settings.system_settings.sections.localization.description' => [
                'en' => 'Configure language, currency, and timezone settings',
                'ar' => 'تكوين إعدادات اللغة والعملة والمنطقة الزمنية',
            ],
            'pages.settings.system_settings.sections.appearance' => [
                'en' => 'Appearance',
                'ar' => 'المظهر',
            ],
            'pages.settings.system_settings.sections.appearance.description' => [
                'en' => 'Customize the visual appearance of the dashboard',
                'ar' => 'تخصيص المظهر المرئي للوحة التحكم',
            ],
            'pages.settings.system_settings.fields.app_name' => [
                'en' => 'Application Name',
                'ar' => 'اسم التطبيق',
            ],
            'pages.settings.system_settings.fields.app_url' => [
                'en' => 'Application URL',
                'ar' => 'رابط التطبيق',
            ],
            'pages.settings.system_settings.fields.default_language' => [
                'en' => 'Default Language',
                'ar' => 'اللغة الافتراضية',
            ],
            'pages.settings.system_settings.fields.default_currency' => [
                'en' => 'Default Currency',
                'ar' => 'العملة الافتراضية',
            ],
            'pages.settings.system_settings.fields.timezone' => [
                'en' => 'Timezone',
                'ar' => 'المنطقة الزمنية',
            ],
            'pages.settings.system_settings.fields.primary_color' => [
                'en' => 'Primary Color',
                'ar' => 'اللون الأساسي',
            ],
            'pages.settings.system_settings.fields.primary_color.helper' => [
                'en' => 'The main color used throughout the dashboard',
                'ar' => 'اللون الرئيسي المستخدم في جميع أنحاء لوحة التحكم',
            ],
            'pages.settings.system_settings.actions.save_settings' => [
                'en' => 'Save Settings',
                'ar' => 'حفظ الإعدادات',
            ],

            // Trial Balance Report Page
            'pages.reports.trial_balance.title' => [
                'en' => 'Trial Balance Report',
                'ar' => 'تقرير ميزان المراجعة',
            ],
            'pages.reports.trial_balance.filters.section' => [
                'en' => 'Filters',
                'ar' => 'التصفية',
            ],
            'pages.reports.trial_balance.filters.as_of_date' => [
                'en' => 'As Of Date',
                'ar' => 'حتى تاريخ',
            ],
            'pages.reports.trial_balance.filters.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'pages.reports.trial_balance.filters.cost_center' => [
                'en' => 'Cost Center',
                'ar' => 'مركز التكلفة',
            ],
            'pages.reports.trial_balance.columns.account_code' => [
                'en' => 'Account Code',
                'ar' => 'رمز الحساب',
            ],
            'pages.reports.trial_balance.columns.account_name' => [
                'en' => 'Account Name',
                'ar' => 'اسم الحساب',
            ],
            'pages.reports.trial_balance.columns.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'pages.reports.trial_balance.columns.debits' => [
                'en' => 'Debits',
                'ar' => 'مدين',
            ],
            'pages.reports.trial_balance.columns.credits' => [
                'en' => 'Credits',
                'ar' => 'دائن',
            ],
            'pages.reports.trial_balance.columns.balance' => [
                'en' => 'Balance',
                'ar' => 'الرصيد',
            ],
            'pages.reports.trial_balance.export_title' => [
                'en' => 'Trial Balance as of :date',
                'ar' => 'ميزان المراجعة حتى تاريخ :date',
            ],
        ];

        // Get all active languages
        $languages = Language::where('is_active', true)->get();

        foreach ($translations as $key => $langTranslations) {
            foreach ($languages as $language) {
                $code = $language->code;
                
                // Only create translation if it exists for this language
                if (isset($langTranslations[$code])) {
                    Translation::updateOrCreate(
                        [
                            'key' => $key,
                            'language_id' => $language->id,
                            'group' => 'dashboard',
                        ],
                        [
                            'value' => $langTranslations[$code],
                        ]
                    );
                }
            }
        }

        $this->command->info('Dashboard translations seeded successfully for all languages!');
    }
}

