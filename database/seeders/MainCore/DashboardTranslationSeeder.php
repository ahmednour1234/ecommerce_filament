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

            // General Ledger Report Page
            'pages.reports.general_ledger.title' => [
                'en' => 'General Ledger Report',
                'ar' => 'تقرير دفتر الأستاذ العام',
            ],

            // Income Statement Report Page
            'pages.reports.income_statement.title' => [
                'en' => 'Income Statement Report',
                'ar' => 'تقرير قائمة الدخل',
            ],

            // Account Statement Report Page
            'pages.reports.account_statement.title' => [
                'en' => 'Account Statement Report',
                'ar' => 'تقرير كشف الحساب',
            ],
            'pages.reports.account_statement.select_account' => [
                'en' => 'Please select an account to view the statement',
                'ar' => 'يرجى اختيار حساب لعرض الكشف',
            ],
            'pages.reports.account_statement.select_account_description' => [
                'en' => 'Choose an account from the filters above to generate the account statement.',
                'ar' => 'اختر حسابًا من عوامل التصفية أعلاه لإنشاء كشف الحساب.',
            ],

            // Balance Sheet Report Page
            'pages.reports.balance_sheet.title' => [
                'en' => 'Balance Sheet Report',
                'ar' => 'تقرير الميزانية العمومية',
            ],

            // Cash Flow Report Page
            'pages.reports.cash_flow.title' => [
                'en' => 'Cash Flow Report',
                'ar' => 'تقرير التدفقات النقدية',
            ],

            // VAT Report Page
            'pages.reports.vat_report.title' => [
                'en' => 'VAT Report',
                'ar' => 'تقرير ضريبة القيمة المضافة',
            ],
            'pages.reports.vat_report.empty_state' => [
                'en' => 'No journal entry lines',
                'ar' => 'لا توجد سطور قيود اليومية',
            ],

            // Fixed Assets Report Page
            'pages.reports.fixed_assets.title' => [
                'en' => 'Fixed Assets Report',
                'ar' => 'تقرير الأصول الثابتة',
            ],
            'pages.reports.fixed_assets.empty_state' => [
                'en' => 'No assets',
                'ar' => 'لا توجد أصول',
            ],

            // Journal Entries By Year Report Page
            'pages.reports.journal_entries_by_year.title' => [
                'en' => 'Journal Entries By Year Report',
                'ar' => 'تقرير قيود اليومية حسب السنة',
            ],
            'pages.reports.journal_entries_by_year.empty_state' => [
                'en' => 'No journal entries',
                'ar' => 'لا توجد قيود يومية',
            ],

            // Accounts Receivable Report Page
            'pages.reports.accounts_receivable.title' => [
                'en' => 'Accounts Receivable Report',
                'ar' => 'تقرير الذمم المدينة',
            ],
            'pages.reports.accounts_receivable.empty_state' => [
                'en' => 'No customers',
                'ar' => 'لا توجد عملاء',
            ],

            // Accounts Payable Aging Current Report Page
            'pages.reports.accounts_payable_aging_current.title' => [
                'en' => 'Accounts Payable Aging Current Report',
                'ar' => 'تقرير أعمار الديون الدائنة الحالية',
            ],
            'pages.reports.accounts_payable_aging_current.empty_state' => [
                'en' => 'No accounts',
                'ar' => 'لا توجد حسابات',
            ],
            'pages.reports.accounts_payable_aging_current.supplier' => [
                'en' => 'Supplier',
                'ar' => 'المورد',
            ],
            'pages.reports.accounts_payable_aging_current.current' => [
                'en' => '0-30 Days',
                'ar' => '0-30 يوم',
            ],
            'pages.reports.accounts_payable_aging_current.days_31_60' => [
                'en' => '31-60 Days',
                'ar' => '31-60 يوم',
            ],
            'pages.reports.accounts_payable_aging_current.days_61_90' => [
                'en' => '61-90 Days',
                'ar' => '61-90 يوم',
            ],
            'pages.reports.accounts_payable_aging_current.over_90' => [
                'en' => 'Over 90 Days',
                'ar' => 'أكثر من 90 يوم',
            ],
            'pages.reports.accounts_payable_aging_current.total' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],

            // Accounts Payable Aging Overdue Report Page
            'pages.reports.accounts_payable_aging_overdue.title' => [
                'en' => 'Accounts Payable Aging Overdue Report',
                'ar' => 'تقرير أعمار الديون الدائنة المتأخرة',
            ],
            'pages.reports.accounts_payable_aging_overdue.empty_state' => [
                'en' => 'No accounts',
                'ar' => 'لا توجد حسابات',
            ],

            // Financial Position Report Page
            'pages.reports.financial_position.title' => [
                'en' => 'Financial Position Report',
                'ar' => 'تقرير المركز المالي',
            ],
            'pages.reports.financial_position.empty_state' => [
                'en' => 'No general ledger entries',
                'ar' => 'لا توجد قيود دفتر الأستاذ العام',
            ],

            // Financial Performance Report Page
            'pages.reports.financial_performance.title' => [
                'en' => 'Financial Performance Report',
                'ar' => 'تقرير الأداء المالي',
            ],
            'pages.reports.financial_performance.empty_state' => [
                'en' => 'No accounts',
                'ar' => 'لا توجد حسابات',
            ],

            // Comparisons Report Page
            'pages.reports.comparisons.title' => [
                'en' => 'Comparisons Report',
                'ar' => 'تقرير المقارنات',
            ],
            'pages.reports.comparisons.period_b' => [
                'en' => 'Period B',
                'ar' => 'الفترة ب',
            ],
            'pages.reports.comparisons.period_b_from' => [
                'en' => 'Period B From Date',
                'ar' => 'تاريخ بداية الفترة ب',
            ],
            'pages.reports.comparisons.period_b_to' => [
                'en' => 'Period B To Date',
                'ar' => 'تاريخ نهاية الفترة ب',
            ],
            'pages.reports.comparisons.error_both_periods_required' => [
                'en' => 'Both periods must be specified for comparison report.',
                'ar' => 'يجب تحديد كلا الفترتين لتقرير المقارنة.',
            ],

            // Accounts Resource - Forms
            'forms.accounts.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.accounts.code.label' => [
                'en' => 'Account Code',
                'ar' => 'رمز الحساب',
            ],
            'forms.accounts.code.helper' => [
                'en' => 'Unique account code (e.g., 1000, 1100, 2000)',
                'ar' => 'رمز حساب فريد (مثل: 1000، 1100، 2000)',
            ],
            'forms.accounts.name.label' => [
                'en' => 'Account Name',
                'ar' => 'اسم الحساب',
            ],
            'forms.accounts.type.label' => [
                'en' => 'Account Type',
                'ar' => 'نوع الحساب',
            ],
            'forms.accounts.type.options.asset' => [
                'en' => 'Asset',
                'ar' => 'أصل',
            ],
            'forms.accounts.type.options.liability' => [
                'en' => 'Liability',
                'ar' => 'التزام',
            ],
            'forms.accounts.type.options.equity' => [
                'en' => 'Equity',
                'ar' => 'حقوق الملكية',
            ],
            'forms.accounts.type.options.revenue' => [
                'en' => 'Revenue',
                'ar' => 'إيراد',
            ],
            'forms.accounts.type.options.expense' => [
                'en' => 'Expense',
                'ar' => 'مصروف',
            ],
            'forms.accounts.parent_id.label' => [
                'en' => 'Parent Account',
                'ar' => 'الحساب الأب',
            ],
            'forms.accounts.parent_id.helper' => [
                'en' => 'Optional: Select a parent account to create a sub-account',
                'ar' => 'اختياري: اختر حسابًا أبًا لإنشاء حساب فرعي',
            ],
            'forms.accounts.level.label' => [
                'en' => 'Level',
                'ar' => 'المستوى',
            ],
            'forms.accounts.level.helper' => [
                'en' => 'Automatically calculated based on parent',
                'ar' => 'يتم حسابه تلقائيًا بناءً على الحساب الأب',
            ],
            'forms.accounts.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.accounts.allow_manual_entry.label' => [
                'en' => 'Allow Manual Entry',
                'ar' => 'السماح بالإدخال اليدوي',
            ],
            'forms.accounts.allow_manual_entry.helper' => [
                'en' => 'Allow manual journal entries to this account',
                'ar' => 'السماح بإدخال قيود يومية يدوية لهذا الحساب',
            ],
            'forms.accounts.notes.label' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],

            // Accounts Resource - Tables
            'tables.accounts.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.accounts.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.accounts.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.accounts.parent' => [
                'en' => 'Parent',
                'ar' => 'الأب',
            ],
            'tables.accounts.level' => [
                'en' => 'Level',
                'ar' => 'المستوى',
            ],
            'tables.accounts.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.accounts.manual_entry' => [
                'en' => 'Manual Entry',
                'ar' => 'إدخال يدوي',
            ],
            'tables.accounts.filters.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.accounts.filters.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.accounts.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'tables.accounts.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.accounts.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],
            'tables.accounts.filters.parent_account' => [
                'en' => 'Parent Account',
                'ar' => 'الحساب الأب',
            ],

            // Journal Entries Resource - Tables
            'tables.journal_entries.entry_number' => [
                'en' => 'Entry Number',
                'ar' => 'رقم القيد',
            ],
            'tables.journal_entries.journal' => [
                'en' => 'Journal',
                'ar' => 'اليومية',
            ],
            'tables.journal_entries.date' => [
                'en' => 'Date',
                'ar' => 'التاريخ',
            ],
            'tables.journal_entries.reference' => [
                'en' => 'Reference',
                'ar' => 'المرجع',
            ],
            'tables.journal_entries.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'tables.journal_entries.cost_center' => [
                'en' => 'Cost Center',
                'ar' => 'مركز التكلفة',
            ],
            'tables.journal_entries.total_debits' => [
                'en' => 'Total Debits',
                'ar' => 'إجمالي المدين',
            ],
            'tables.journal_entries.total_credits' => [
                'en' => 'Total Credits',
                'ar' => 'إجمالي الدائن',
            ],

            // Vouchers Resource - Tables
            'tables.vouchers.voucher_number' => [
                'en' => 'Voucher Number',
                'ar' => 'رقم السند',
            ],
            'tables.vouchers.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.vouchers.types.payment' => [
                'en' => 'Payment',
                'ar' => 'صرف',
            ],
            'tables.vouchers.types.receipt' => [
                'en' => 'Receipt',
                'ar' => 'قبض',
            ],
            'tables.vouchers.date' => [
                'en' => 'Date',
                'ar' => 'التاريخ',
            ],
            'tables.vouchers.amount' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'tables.vouchers.account_code' => [
                'en' => 'Account Code',
                'ar' => 'رمز الحساب',
            ],
            'tables.vouchers.account' => [
                'en' => 'Account',
                'ar' => 'الحساب',
            ],
            'tables.vouchers.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'tables.vouchers.journal_entry' => [
                'en' => 'Journal Entry',
                'ar' => 'قيد اليومية',
            ],

            // Cost Centers Resource - Forms
            'forms.cost_centers.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.cost_centers.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.cost_centers.code.helper' => [
                'en' => 'Unique code for the cost center',
                'ar' => 'رمز فريد لمركز التكلفة',
            ],
            'forms.cost_centers.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.cost_centers.type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'forms.cost_centers.type.options.department' => [
                'en' => 'Department',
                'ar' => 'قسم',
            ],
            'forms.cost_centers.type.options.project' => [
                'en' => 'Project',
                'ar' => 'مشروع',
            ],
            'forms.cost_centers.type.options.location' => [
                'en' => 'Location',
                'ar' => 'موقع',
            ],
            'forms.cost_centers.type.options.other' => [
                'en' => 'Other',
                'ar' => 'أخرى',
            ],
            'forms.cost_centers.type.helper' => [
                'en' => 'Type of cost center (e.g., department, project, location)',
                'ar' => 'نوع مركز التكلفة (مثل: قسم، مشروع، موقع)',
            ],
            'forms.cost_centers.parent_id.label' => [
                'en' => 'Parent Cost Center',
                'ar' => 'مركز التكلفة الأب',
            ],
            'forms.cost_centers.parent_id.helper' => [
                'en' => 'Optional: Select a parent cost center if this is a sub-cost center',
                'ar' => 'اختياري: اختر مركز تكلفة أب إذا كان هذا مركز تكلفة فرعي',
            ],
            'forms.cost_centers.description.label' => [
                'en' => 'Description',
                'ar' => 'الوصف',
            ],
            'forms.cost_centers.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],

            // Cost Centers Resource - Tables
            'tables.cost_centers.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.cost_centers.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.cost_centers.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.cost_centers.parent' => [
                'en' => 'Parent',
                'ar' => 'الأب',
            ],
            'tables.cost_centers.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.cost_centers.filters.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.cost_centers.filters.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.cost_centers.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'tables.cost_centers.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.cost_centers.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],
            'tables.cost_centers.filters.parent_cost_center' => [
                'en' => 'Parent Cost Center',
                'ar' => 'مركز التكلفة الأب',
            ],

            // Assets Resource - Forms
            'forms.assets.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.assets.type.options.fixed' => [
                'en' => 'Fixed Asset',
                'ar' => 'أصل ثابت',
            ],
            'forms.assets.type.options.intangible' => [
                'en' => 'Intangible Asset',
                'ar' => 'أصل غير ملموس',
            ],
            'forms.assets.type.options.current' => [
                'en' => 'Current Asset',
                'ar' => 'أصل متداول',
            ],
            'forms.assets.type.options.investment' => [
                'en' => 'Investment',
                'ar' => 'استثمار',
            ],
            'forms.assets.category.options.property' => [
                'en' => 'Property',
                'ar' => 'عقار',
            ],
            'forms.assets.category.options.equipment' => [
                'en' => 'Equipment',
                'ar' => 'معدات',
            ],
            'forms.assets.category.options.vehicle' => [
                'en' => 'Vehicle',
                'ar' => 'مركبة',
            ],
            'forms.assets.category.options.furniture' => [
                'en' => 'Furniture',
                'ar' => 'أثاث',
            ],
            'forms.assets.category.options.computer' => [
                'en' => 'Computer',
                'ar' => 'كمبيوتر',
            ],
            'forms.assets.category.options.other' => [
                'en' => 'Other',
                'ar' => 'أخرى',
            ],
            'forms.assets.status.options.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.assets.status.options.deprecated' => [
                'en' => 'Deprecated',
                'ar' => 'مستنفذ',
            ],
            'forms.assets.status.options.disposed' => [
                'en' => 'Disposed',
                'ar' => 'متصرف به',
            ],
            'forms.assets.status.options.maintenance' => [
                'en' => 'Maintenance',
                'ar' => 'صيانة',
            ],
            'forms.assets.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.assets.code.label' => [
                'en' => 'Asset Code',
                'ar' => 'رمز الأصل',
            ],
            'forms.assets.name.label' => [
                'en' => 'Asset Name',
                'ar' => 'اسم الأصل',
            ],
            'forms.assets.description.label' => [
                'en' => 'Description',
                'ar' => 'الوصف',
            ],
            'forms.assets.account_id.label' => [
                'en' => 'Asset Account',
                'ar' => 'حساب الأصل',
            ],
            'forms.assets.type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'forms.assets.category.label' => [
                'en' => 'Category',
                'ar' => 'الفئة',
            ],
            'forms.assets.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.assets.sections.financial_information' => [
                'en' => 'Financial Information',
                'ar' => 'المعلومات المالية',
            ],
            'forms.assets.purchase_cost.label' => [
                'en' => 'Purchase Cost',
                'ar' => 'تكلفة الشراء',
            ],
            'forms.assets.current_value.label' => [
                'en' => 'Current Value',
                'ar' => 'القيمة الحالية',
            ],
            'forms.assets.purchase_date.label' => [
                'en' => 'Purchase Date',
                'ar' => 'تاريخ الشراء',
            ],
            'forms.assets.useful_life_years.label' => [
                'en' => 'Useful Life (Years)',
                'ar' => 'العمر الإنتاجي (بالسنوات)',
            ],
            'forms.assets.depreciation_rate.label' => [
                'en' => 'Depreciation Rate (%)',
                'ar' => 'معدل الإهلاك (%)',
            ],
            'forms.assets.sections.location_details' => [
                'en' => 'Location & Details',
                'ar' => 'الموقع والتفاصيل',
            ],
            'forms.assets.branch_id.label' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'forms.assets.cost_center_id.label' => [
                'en' => 'Cost Center',
                'ar' => 'مركز التكلفة',
            ],
            'forms.assets.location.label' => [
                'en' => 'Location',
                'ar' => 'الموقع',
            ],
            'forms.assets.serial_number.label' => [
                'en' => 'Serial Number',
                'ar' => 'الرقم التسلسلي',
            ],

            // Assets Resource - Tables
            'tables.assets.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.assets.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.assets.account' => [
                'en' => 'Account',
                'ar' => 'الحساب',
            ],
            'tables.assets.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.assets.category' => [
                'en' => 'Category',
                'ar' => 'الفئة',
            ],
            'tables.assets.purchase_cost' => [
                'en' => 'Purchase Cost',
                'ar' => 'تكلفة الشراء',
            ],
            'tables.assets.current_value' => [
                'en' => 'Current Value',
                'ar' => 'القيمة الحالية',
            ],
            'tables.assets.book_value' => [
                'en' => 'Book Value',
                'ar' => 'القيمة الدفترية',
            ],
            'tables.assets.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.assets.filters.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.assets.filters.category' => [
                'en' => 'Category',
                'ar' => 'الفئة',
            ],
            'tables.assets.filters.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.assets.filters.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],

            // Fiscal Years Resource - Tables
            'tables.fiscal_years.fiscal_year' => [
                'en' => 'Fiscal Year',
                'ar' => 'السنة المالية',
            ],
            'tables.fiscal_years.start_date' => [
                'en' => 'Start Date',
                'ar' => 'تاريخ البدء',
            ],
            'tables.fiscal_years.end_date' => [
                'en' => 'End Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'tables.fiscal_years.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.fiscal_years.closed' => [
                'en' => 'Closed',
                'ar' => 'مغلق',
            ],
            'tables.fiscal_years.periods' => [
                'en' => 'Periods',
                'ar' => 'الفترات',
            ],
            'tables.fiscal_years.filters.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.fiscal_years.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'tables.fiscal_years.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.fiscal_years.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],
            'tables.fiscal_years.filters.closed' => [
                'en' => 'Closed',
                'ar' => 'مغلق',
            ],
            'tables.fiscal_years.filters.closed_only' => [
                'en' => 'Closed only',
                'ar' => 'المغلقة فقط',
            ],
            'tables.fiscal_years.filters.open_only' => [
                'en' => 'Open only',
                'ar' => 'المفتوحة فقط',
            ],

            // Periods Resource - Tables
            'tables.periods.fiscal_year' => [
                'en' => 'Fiscal Year',
                'ar' => 'السنة المالية',
            ],
            'tables.periods.period' => [
                'en' => 'Period',
                'ar' => 'الفترة',
            ],
            'tables.periods.period_number' => [
                'en' => 'Period #',
                'ar' => 'رقم الفترة',
            ],
            'tables.periods.start_date' => [
                'en' => 'Start Date',
                'ar' => 'تاريخ البدء',
            ],
            'tables.periods.end_date' => [
                'en' => 'End Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'tables.periods.closed' => [
                'en' => 'Closed',
                'ar' => 'مغلق',
            ],
            'tables.periods.filters.fiscal_year' => [
                'en' => 'Fiscal Year',
                'ar' => 'السنة المالية',
            ],
            'tables.periods.filters.closed' => [
                'en' => 'Closed',
                'ar' => 'مغلق',
            ],
            'tables.periods.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'tables.periods.filters.closed_only' => [
                'en' => 'Closed only',
                'ar' => 'المغلقة فقط',
            ],
            'tables.periods.filters.open_only' => [
                'en' => 'Open only',
                'ar' => 'المفتوحة فقط',
            ],

            // Projects Resource - Tables
            'tables.projects.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.projects.name' => [
                'en' => 'Project Name',
                'ar' => 'اسم المشروع',
            ],
            'tables.projects.start_date' => [
                'en' => 'Start Date',
                'ar' => 'تاريخ البدء',
            ],
            'tables.projects.end_date' => [
                'en' => 'End Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'tables.projects.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.projects.filters.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.projects.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'tables.projects.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.projects.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Bank Accounts Resource - Tables
            'tables.bank_accounts.account_code' => [
                'en' => 'Account Code',
                'ar' => 'رمز الحساب',
            ],
            'tables.bank_accounts.account_name' => [
                'en' => 'Account Name',
                'ar' => 'اسم الحساب',
            ],
            'tables.bank_accounts.bank_name' => [
                'en' => 'Bank Name',
                'ar' => 'اسم البنك',
            ],
            'tables.bank_accounts.account_number' => [
                'en' => 'Account Number',
                'ar' => 'رقم الحساب',
            ],
            'tables.bank_accounts.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'tables.bank_accounts.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'tables.bank_accounts.current_balance' => [
                'en' => 'Current Balance',
                'ar' => 'الرصيد الحالي',
            ],
            'tables.bank_accounts.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.bank_accounts.filters.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'tables.bank_accounts.filters.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'tables.bank_accounts.filters.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'tables.bank_accounts.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'tables.bank_accounts.filters.active_only' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'tables.bank_accounts.filters.inactive_only' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Bank Accounts Resource - Forms
            'forms.bank_accounts.sections.bank_account_information' => [
                'en' => 'Bank Account Information',
                'ar' => 'معلومات الحساب البنكي',
            ],
            'forms.bank_accounts.account_id.label' => [
                'en' => 'Account',
                'ar' => 'الحساب',
            ],
            'forms.bank_accounts.account_id.helper' => [
                'en' => 'Select the account associated with this bank account',
                'ar' => 'اختر الحساب المرتبط بهذا الحساب البنكي',
            ],
            'forms.bank_accounts.bank_name.label' => [
                'en' => 'Bank Name',
                'ar' => 'اسم البنك',
            ],
            'forms.bank_accounts.account_number.label' => [
                'en' => 'Account Number',
                'ar' => 'رقم الحساب',
            ],
            'forms.bank_accounts.iban.label' => [
                'en' => 'IBAN',
                'ar' => 'رقم الآيبان',
            ],
            'forms.bank_accounts.swift_code.label' => [
                'en' => 'SWIFT Code',
                'ar' => 'رمز السويفت',
            ],
            'forms.bank_accounts.branch_id.label' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'forms.bank_accounts.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.bank_accounts.opening_balance.label' => [
                'en' => 'Opening Balance',
                'ar' => 'الرصيد الافتتاحي',
            ],
            'forms.bank_accounts.current_balance.label' => [
                'en' => 'Current Balance',
                'ar' => 'الرصيد الحالي',
            ],
            'forms.bank_accounts.current_balance.helper' => [
                'en' => 'Calculated from opening balance + all posted transactions. Automatically updated when transactions are posted.',
                'ar' => 'يتم حسابه من الرصيد الافتتاحي + جميع المعاملات المرحلة. يتم تحديثه تلقائيًا عند ترحيل المعاملات.',
            ],
            'forms.bank_accounts.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.bank_accounts.notes.label' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            'tables.bank_accounts.actions.reconcile' => [
                'en' => 'Reconcile',
                'ar' => 'التسوية',
            ],

            // Categories Resource - Forms
            'forms.categories.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.categories.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.categories.slug.label' => [
                'en' => 'Slug',
                'ar' => 'الرابط',
            ],
            'forms.categories.slug.helper_text' => [
                'en' => 'Auto-generated from name',
                'ar' => 'يتم إنشاؤه تلقائيًا من الاسم',
            ],
            'forms.categories.parent_id.label' => [
                'en' => 'Parent Category',
                'ar' => 'الفئة الأب',
            ],
            'forms.categories.description.label' => [
                'en' => 'Description',
                'ar' => 'الوصف',
            ],
            'forms.categories.image.label' => [
                'en' => 'Image',
                'ar' => 'الصورة',
            ],
            'forms.categories.sort_order.label' => [
                'en' => 'Sort Order',
                'ar' => 'ترتيب العرض',
            ],
            'forms.categories.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            // Categories Resource - Tables
            'tables.categories.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.categories.parent' => [
                'en' => 'Parent',
                'ar' => 'الأب',
            ],
            'tables.categories.image' => [
                'en' => 'Image',
                'ar' => 'الصورة',
            ],
            'tables.categories.products' => [
                'en' => 'Products',
                'ar' => 'المنتجات',
            ],
            'tables.categories.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            // Categories Resource - Filters
            'filters.categories.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'filters.categories.is_active.placeholder' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'filters.categories.is_active.true_label' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'filters.categories.is_active.false_label' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Brands Resource - Forms
            'forms.brands.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.brands.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.brands.slug.label' => [
                'en' => 'Slug',
                'ar' => 'الرابط',
            ],
            'forms.brands.slug.helper_text' => [
                'en' => 'Auto-generated from name',
                'ar' => 'يتم إنشاؤه تلقائيًا من الاسم',
            ],
            'forms.brands.logo.label' => [
                'en' => 'Logo',
                'ar' => 'الشعار',
            ],
            'forms.brands.description.label' => [
                'en' => 'Description',
                'ar' => 'الوصف',
            ],
            'forms.brands.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            // Brands Resource - Tables
            'tables.brands.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.brands.logo' => [
                'en' => 'Logo',
                'ar' => 'الشعار',
            ],
            'tables.brands.products' => [
                'en' => 'Products',
                'ar' => 'المنتجات',
            ],
            'tables.brands.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            // Brands Resource - Filters
            'filters.brands.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'filters.brands.is_active.placeholder' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'filters.brands.is_active.true_label' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'filters.brands.is_active.false_label' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Products Resource - Forms
            'forms.products.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.products.sku.label' => [
                'en' => 'SKU',
                'ar' => 'رمز المنتج',
            ],
            'forms.products.sku.helper_text' => [
                'en' => 'Stock Keeping Unit - unique identifier',
                'ar' => 'وحدة حفظ المخزون - معرف فريد',
            ],
            'forms.products.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.products.slug.label' => [
                'en' => 'Slug',
                'ar' => 'الرابط',
            ],
            'forms.products.slug.helper_text' => [
                'en' => 'Auto-generated from name',
                'ar' => 'يتم إنشاؤه تلقائيًا من الاسم',
            ],
            'forms.products.type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'forms.products.type.options.product' => [
                'en' => 'Product',
                'ar' => 'منتج',
            ],
            'forms.products.type.options.service' => [
                'en' => 'Service',
                'ar' => 'خدمة',
            ],
            'forms.products.category_id.label' => [
                'en' => 'Category',
                'ar' => 'الفئة',
            ],
            'forms.products.brand_id.label' => [
                'en' => 'Brand',
                'ar' => 'العلامة التجارية',
            ],
            'forms.products.description.label' => [
                'en' => 'Description',
                'ar' => 'الوصف',
            ],
            'forms.products.sections.pricing_inventory' => [
                'en' => 'Pricing & Inventory',
                'ar' => 'التسعير والمخزون',
            ],
            'forms.products.price.label' => [
                'en' => 'Price',
                'ar' => 'السعر',
            ],
            'forms.products.cost.label' => [
                'en' => 'Cost',
                'ar' => 'التكلفة',
            ],
            'forms.products.cost.helper_text' => [
                'en' => 'Cost price for profit calculation',
                'ar' => 'سعر التكلفة لحساب الربح',
            ],
            'forms.products.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.products.stock_quantity.label' => [
                'en' => 'Stock Quantity',
                'ar' => 'كمية المخزون',
            ],
            'forms.products.track_inventory.label' => [
                'en' => 'Track Inventory',
                'ar' => 'تتبع المخزون',
            ],
            'forms.products.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.products.sections.warehouses' => [
                'en' => 'Warehouses',
                'ar' => 'المستودعات',
            ],
            'forms.products.sections.batches' => [
                'en' => 'Batches',
                'ar' => 'الدفعات',
            ],
            'forms.products.sections.additional_information' => [
                'en' => 'Additional Information',
                'ar' => 'معلومات إضافية',
            ],
            // Products Resource - Tables
            'tables.products.sku' => [
                'en' => 'Sku',
                'ar' => 'رمز المنتج',
            ],
            'tables.products.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.products.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.products.category' => [
                'en' => 'Category',
                'ar' => 'الفئة',
            ],
            'tables.products.brand' => [
                'en' => 'Brand',
                'ar' => 'العلامة التجارية',
            ],
            'tables.products.price' => [
                'en' => 'Price',
                'ar' => 'السعر',
            ],
            'tables.products.stock' => [
                'en' => 'Stock',
                'ar' => 'المخزون',
            ],
            'tables.products.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            // Products Resource - Filters
            'filters.products.type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'filters.products.type.options.product' => [
                'en' => 'Product',
                'ar' => 'منتج',
            ],
            'filters.products.type.options.service' => [
                'en' => 'Service',
                'ar' => 'خدمة',
            ],
            'filters.products.category_id.label' => [
                'en' => 'Category',
                'ar' => 'الفئة',
            ],
            'filters.products.brand_id.label' => [
                'en' => 'Brand',
                'ar' => 'العلامة التجارية',
            ],
            'filters.products.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'filters.products.is_active.placeholder' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'filters.products.is_active.true_label' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'filters.products.is_active.false_label' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],
            'filters.products.stock_quantity.label' => [
                'en' => 'Stock Quantity',
                'ar' => 'كمية المخزون',
            ],
            'filters.products.stock_from.label' => [
                'en' => 'Stock From',
                'ar' => 'المخزون من',
            ],
            'filters.products.stock_to.label' => [
                'en' => 'Stock To',
                'ar' => 'المخزون إلى',
            ],

            // Batches Resource - Forms
            'forms.batches.sections.batch_information' => [
                'en' => 'Batch Information',
                'ar' => 'معلومات الدفعة',
            ],
            'forms.batches.product_id.label' => [
                'en' => 'Product',
                'ar' => 'المنتج',
            ],
            'forms.batches.warehouse_id.label' => [
                'en' => 'Warehouse',
                'ar' => 'المستودع',
            ],
            'forms.batches.batch_number.label' => [
                'en' => 'Batch Number',
                'ar' => 'رقم الدفعة',
            ],
            'forms.batches.manufacturing_date.label' => [
                'en' => 'Manufacturing Date',
                'ar' => 'تاريخ التصنيع',
            ],
            'forms.batches.expiry_date.label' => [
                'en' => 'Expiry Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'forms.batches.quantity.label' => [
                'en' => 'Quantity',
                'ar' => 'الكمية',
            ],
            'forms.batches.cost.label' => [
                'en' => 'Cost',
                'ar' => 'التكلفة',
            ],
            'forms.batches.supplier_reference.label' => [
                'en' => 'Supplier Reference',
                'ar' => 'مرجع المورد',
            ],
            'forms.batches.notes.label' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            // Batches Resource - Tables
            'tables.batches.batch_number' => [
                'en' => 'Batch Number',
                'ar' => 'رقم الدفعة',
            ],
            'tables.batches.product' => [
                'en' => 'Product',
                'ar' => 'المنتج',
            ],
            'tables.batches.warehouse' => [
                'en' => 'Warehouse',
                'ar' => 'المستودع',
            ],
            'tables.batches.manufacturing_date' => [
                'en' => 'Manufacturing Date',
                'ar' => 'تاريخ التصنيع',
            ],
            'tables.batches.expiry_date' => [
                'en' => 'Expiry Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'tables.batches.quantity' => [
                'en' => 'Quantity',
                'ar' => 'الكمية',
            ],
            'tables.batches.cost' => [
                'en' => 'Cost',
                'ar' => 'التكلفة',
            ],
            'tables.batches.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            // Batches Resource - Filters
            'filters.batches.product_id.label' => [
                'en' => 'Product',
                'ar' => 'المنتج',
            ],
            'filters.batches.warehouse_id.label' => [
                'en' => 'Warehouse',
                'ar' => 'المستودع',
            ],
            'filters.batches.expired.label' => [
                'en' => 'Expired',
                'ar' => 'منتهي الصلاحية',
            ],
            'filters.batches.expiring_soon.label' => [
                'en' => 'Expiring Soon',
                'ar' => 'ينتهي قريبًا',
            ],

            // Customers Resource - Forms
            'forms.customers.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.customers.code.label' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'forms.customers.code.helper_text' => [
                'en' => 'Unique customer code',
                'ar' => 'رمز العميل الفريد',
            ],
            'forms.customers.name.label' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.customers.email.label' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'forms.customers.phone.label' => [
                'en' => 'Phone',
                'ar' => 'الهاتف',
            ],
            'forms.customers.sections.address' => [
                'en' => 'Address',
                'ar' => 'العنوان',
            ],
            'forms.customers.address.label' => [
                'en' => 'Address',
                'ar' => 'العنوان',
            ],
            'forms.customers.city.label' => [
                'en' => 'City',
                'ar' => 'المدينة',
            ],
            'forms.customers.state.label' => [
                'en' => 'State',
                'ar' => 'الولاية',
            ],
            'forms.customers.country.label' => [
                'en' => 'Country',
                'ar' => 'الدولة',
            ],
            'forms.customers.postal_code.label' => [
                'en' => 'Postal Code',
                'ar' => 'الرمز البريدي',
            ],
            'forms.customers.sections.financial' => [
                'en' => 'Financial',
                'ar' => 'المالية',
            ],
            'forms.customers.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.customers.credit_limit.label' => [
                'en' => 'Credit Limit',
                'ar' => 'حد الائتمان',
            ],
            'forms.customers.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            // Customers Resource - Tables
            'tables.customers.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'tables.customers.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.customers.email' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'tables.customers.phone' => [
                'en' => 'Phone',
                'ar' => 'الهاتف',
            ],
            'tables.customers.credit_limit' => [
                'en' => 'Credit Limit',
                'ar' => 'حد الائتمان',
            ],
            'tables.customers.orders' => [
                'en' => 'Orders',
                'ar' => 'الطلبات',
            ],
            'tables.customers.is_active' => [
                'en' => 'Is Active',
                'ar' => 'نشط',
            ],
            // Customers Resource - Filters
            'filters.customers.is_active.label' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'filters.customers.is_active.placeholder' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'filters.customers.is_active.true_label' => [
                'en' => 'Active only',
                'ar' => 'النشطة فقط',
            ],
            'filters.customers.is_active.false_label' => [
                'en' => 'Inactive only',
                'ar' => 'غير النشطة فقط',
            ],

            // Orders Resource - Forms
            'forms.orders.sections.order_information' => [
                'en' => 'Order Information',
                'ar' => 'معلومات الطلب',
            ],
            'forms.orders.order_number.label' => [
                'en' => 'Order Number',
                'ar' => 'رقم الطلب',
            ],
            'forms.orders.order_date.label' => [
                'en' => 'Order Date',
                'ar' => 'تاريخ الطلب',
            ],
            'forms.orders.customer_id.label' => [
                'en' => 'Customer',
                'ar' => 'العميل',
            ],
            'forms.orders.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.orders.status.options.pending' => [
                'en' => 'Pending',
                'ar' => 'قيد الانتظار',
            ],
            'forms.orders.status.options.processing' => [
                'en' => 'Processing',
                'ar' => 'قيد المعالجة',
            ],
            'forms.orders.status.options.completed' => [
                'en' => 'Completed',
                'ar' => 'مكتمل',
            ],
            'forms.orders.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'forms.orders.status.options.refunded' => [
                'en' => 'Refunded',
                'ar' => 'مسترد',
            ],
            'forms.orders.sections.financial_information' => [
                'en' => 'Financial Information',
                'ar' => 'المعلومات المالية',
            ],
            'forms.orders.branch_id.label' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'forms.orders.cost_center_id.label' => [
                'en' => 'Cost Center',
                'ar' => 'مركز التكلفة',
            ],
            'forms.orders.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.orders.subtotal.label' => [
                'en' => 'Subtotal',
                'ar' => 'المجموع الفرعي',
            ],
            'forms.orders.tax_amount.label' => [
                'en' => 'Tax Amount',
                'ar' => 'مبلغ الضريبة',
            ],
            'forms.orders.discount_amount.label' => [
                'en' => 'Discount Amount',
                'ar' => 'مبلغ الخصم',
            ],
            'forms.orders.total.label' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],
            'forms.orders.sections.order_items' => [
                'en' => 'Order Items',
                'ar' => 'عناصر الطلب',
            ],
            'forms.orders.items.product_id.label' => [
                'en' => 'Product',
                'ar' => 'المنتج',
            ],
            'forms.orders.items.quantity.label' => [
                'en' => 'Quantity',
                'ar' => 'الكمية',
            ],
            'forms.orders.items.unit_price.label' => [
                'en' => 'Unit Price',
                'ar' => 'سعر الوحدة',
            ],
            'forms.orders.items.discount.label' => [
                'en' => 'Discount',
                'ar' => 'الخصم',
            ],
            'forms.orders.items.total.label' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],
            'forms.orders.sections.notes' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            'forms.orders.notes.label' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            // Orders Resource - Tables
            'tables.orders.order_number' => [
                'en' => 'Order #',
                'ar' => 'رقم الطلب',
            ],
            'tables.orders.date' => [
                'en' => 'Date',
                'ar' => 'التاريخ',
            ],
            'tables.orders.customer' => [
                'en' => 'Customer',
                'ar' => 'العميل',
            ],
            'tables.orders.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.orders.total' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],
            'tables.orders.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            // Orders Resource - Filters
            'filters.orders.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'filters.orders.status.options.pending' => [
                'en' => 'Pending',
                'ar' => 'قيد الانتظار',
            ],
            'filters.orders.status.options.processing' => [
                'en' => 'Processing',
                'ar' => 'قيد المعالجة',
            ],
            'filters.orders.status.options.completed' => [
                'en' => 'Completed',
                'ar' => 'مكتمل',
            ],
            'filters.orders.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'filters.orders.status.options.refunded' => [
                'en' => 'Refunded',
                'ar' => 'مسترد',
            ],
            'filters.orders.customer_id.label' => [
                'en' => 'Customer',
                'ar' => 'العميل',
            ],
            'filters.orders.order_date.label' => [
                'en' => 'Order Date',
                'ar' => 'تاريخ الطلب',
            ],
            'filters.orders.created_from.label' => [
                'en' => 'From',
                'ar' => 'من',
            ],
            'filters.orders.created_until.label' => [
                'en' => 'Until',
                'ar' => 'إلى',
            ],

            // Invoices Resource - Forms
            'forms.invoices.sections.invoice_information' => [
                'en' => 'Invoice Information',
                'ar' => 'معلومات الفاتورة',
            ],
            'forms.invoices.invoice_number.label' => [
                'en' => 'Invoice Number',
                'ar' => 'رقم الفاتورة',
            ],
            'forms.invoices.invoice_date.label' => [
                'en' => 'Invoice Date',
                'ar' => 'تاريخ الفاتورة',
            ],
            'forms.invoices.order_id.label' => [
                'en' => 'Order',
                'ar' => 'الطلب',
            ],
            'forms.invoices.customer_id.label' => [
                'en' => 'Customer',
                'ar' => 'العميل',
            ],
            'forms.invoices.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.invoices.status.options.draft' => [
                'en' => 'Draft',
                'ar' => 'مسودة',
            ],
            'forms.invoices.status.options.sent' => [
                'en' => 'Sent',
                'ar' => 'مرسلة',
            ],
            'forms.invoices.status.options.paid' => [
                'en' => 'Paid',
                'ar' => 'مدفوعة',
            ],
            'forms.invoices.status.options.partial' => [
                'en' => 'Partial',
                'ar' => 'جزئية',
            ],
            'forms.invoices.status.options.overdue' => [
                'en' => 'Overdue',
                'ar' => 'متأخرة',
            ],
            'forms.invoices.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغية',
            ],
            'forms.invoices.due_date.label' => [
                'en' => 'Due Date',
                'ar' => 'تاريخ الاستحقاق',
            ],
            'forms.invoices.paid_at.label' => [
                'en' => 'Paid At',
                'ar' => 'تاريخ الدفع',
            ],
            'forms.invoices.sections.financial_information' => [
                'en' => 'Financial Information',
                'ar' => 'المعلومات المالية',
            ],
            'forms.invoices.currency_id.label' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.invoices.subtotal.label' => [
                'en' => 'Subtotal',
                'ar' => 'المجموع الفرعي',
            ],
            'forms.invoices.tax_amount.label' => [
                'en' => 'Tax Amount',
                'ar' => 'مبلغ الضريبة',
            ],
            'forms.invoices.discount_amount.label' => [
                'en' => 'Discount Amount',
                'ar' => 'مبلغ الخصم',
            ],
            'forms.invoices.total.label' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],
            'forms.invoices.sections.invoice_items' => [
                'en' => 'Invoice Items',
                'ar' => 'عناصر الفاتورة',
            ],
            'forms.invoices.items.product_id.label' => [
                'en' => 'Product',
                'ar' => 'المنتج',
            ],
            'forms.invoices.items.description.label' => [
                'en' => 'Description',
                'ar' => 'الوصف',
            ],
            'forms.invoices.items.quantity.label' => [
                'en' => 'Quantity',
                'ar' => 'الكمية',
            ],
            'forms.invoices.items.unit_price.label' => [
                'en' => 'Unit Price',
                'ar' => 'سعر الوحدة',
            ],
            'forms.invoices.items.discount.label' => [
                'en' => 'Discount',
                'ar' => 'الخصم',
            ],
            'forms.invoices.items.total.label' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],
            // Invoices Resource - Tables
            'tables.invoices.invoice_number' => [
                'en' => 'Invoice #',
                'ar' => 'رقم الفاتورة',
            ],
            'tables.invoices.date' => [
                'en' => 'Date',
                'ar' => 'التاريخ',
            ],
            'tables.invoices.customer' => [
                'en' => 'Customer',
                'ar' => 'العميل',
            ],
            'tables.invoices.order' => [
                'en' => 'Order',
                'ar' => 'الطلب',
            ],
            'tables.invoices.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.invoices.total' => [
                'en' => 'Total',
                'ar' => 'الإجمالي',
            ],
            'tables.invoices.due_date' => [
                'en' => 'Due Date',
                'ar' => 'تاريخ الاستحقاق',
            ],
            'tables.invoices.paid_at' => [
                'en' => 'Paid At',
                'ar' => 'تاريخ الدفع',
            ],
            // Invoices Resource - Filters
            'filters.invoices.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'filters.invoices.status.options.draft' => [
                'en' => 'Draft',
                'ar' => 'مسودة',
            ],
            'filters.invoices.status.options.sent' => [
                'en' => 'Sent',
                'ar' => 'مرسلة',
            ],
            'filters.invoices.status.options.paid' => [
                'en' => 'Paid',
                'ar' => 'مدفوعة',
            ],
            'filters.invoices.status.options.partial' => [
                'en' => 'Partial',
                'ar' => 'جزئية',
            ],
            'filters.invoices.status.options.overdue' => [
                'en' => 'Overdue',
                'ar' => 'متأخرة',
            ],
            'filters.invoices.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغية',
            ],
            'filters.invoices.customer_id.label' => [
                'en' => 'Customer',
                'ar' => 'العميل',
            ],
            'filters.invoices.overdue.label' => [
                'en' => 'Overdue Invoices',
                'ar' => 'الفواتير المتأخرة',
            ],

            // Installments Resource - Forms
            'forms.installments.sections.installment_information' => [
                'en' => 'Installment Information',
                'ar' => 'معلومات القسط',
            ],
            'forms.installments.installment_number.label' => [
                'en' => 'Installment Number',
                'ar' => 'رقم القسط',
            ],
            'forms.installments.installmentable_type.label' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'forms.installments.installmentable_type.options.order' => [
                'en' => 'Order',
                'ar' => 'الطلب',
            ],
            'forms.installments.installmentable_type.options.invoice' => [
                'en' => 'Invoice',
                'ar' => 'الفاتورة',
            ],
            'forms.installments.installmentable_id.label' => [
                'en' => 'Order/Invoice',
                'ar' => 'الطلب/الفاتورة',
            ],
            'forms.installments.amount.label' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'forms.installments.due_date.label' => [
                'en' => 'Due Date',
                'ar' => 'تاريخ الاستحقاق',
            ],
            'forms.installments.paid_date.label' => [
                'en' => 'Paid Date',
                'ar' => 'تاريخ الدفع',
            ],
            'forms.installments.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.installments.status.options.pending' => [
                'en' => 'Pending',
                'ar' => 'قيد الانتظار',
            ],
            'forms.installments.status.options.paid' => [
                'en' => 'Paid',
                'ar' => 'مدفوع',
            ],
            'forms.installments.status.options.overdue' => [
                'en' => 'Overdue',
                'ar' => 'متأخر',
            ],
            'forms.installments.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'forms.installments.payment_method_id.label' => [
                'en' => 'Payment Method',
                'ar' => 'طريقة الدفع',
            ],
            'forms.installments.payment_reference.label' => [
                'en' => 'Payment Reference',
                'ar' => 'مرجع الدفع',
            ],
            'forms.installments.notes.label' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            // Installments Resource - Tables
            'tables.installments.installment_number' => [
                'en' => 'Installment #',
                'ar' => 'رقم القسط',
            ],
            'tables.installments.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'tables.installments.order_invoice' => [
                'en' => 'Order/Invoice',
                'ar' => 'الطلب/الفاتورة',
            ],
            'tables.installments.amount' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'tables.installments.due_date' => [
                'en' => 'Due Date',
                'ar' => 'تاريخ الاستحقاق',
            ],
            'tables.installments.paid_date' => [
                'en' => 'Paid Date',
                'ar' => 'تاريخ الدفع',
            ],
            'tables.installments.remaining' => [
                'en' => 'Remaining',
                'ar' => 'المتبقي',
            ],
            'tables.installments.payment_method' => [
                'en' => 'Payment Method',
                'ar' => 'طريقة الدفع',
            ],
            'tables.installments.reference' => [
                'en' => 'Reference',
                'ar' => 'المرجع',
            ],
            'tables.installments.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            // Installments Resource - Filters
            'filters.installments.status.label' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'filters.installments.status.options.pending' => [
                'en' => 'Pending',
                'ar' => 'قيد الانتظار',
            ],
            'filters.installments.status.options.paid' => [
                'en' => 'Paid',
                'ar' => 'مدفوع',
            ],
            'filters.installments.status.options.overdue' => [
                'en' => 'Overdue',
                'ar' => 'متأخر',
            ],
            'filters.installments.status.options.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'filters.installments.overdue.label' => [
                'en' => 'Overdue Installments',
                'ar' => 'الأقساط المتأخرة',
            ],

            // Accounts Tree Page
            'pages.accounts_tree.filters.all' => [
                'en' => 'All',
                'ar' => 'الكل',
            ],
            'pages.accounts_tree.filters.assets' => [
                'en' => 'Assets',
                'ar' => 'الأصول',
            ],
            'pages.accounts_tree.filters.liabilities' => [
                'en' => 'Liabilities',
                'ar' => 'الالتزامات',
            ],
            'pages.accounts_tree.filters.equity' => [
                'en' => 'Equity',
                'ar' => 'حقوق الملكية',
            ],
            'pages.accounts_tree.filters.revenue' => [
                'en' => 'Revenue',
                'ar' => 'الإيرادات',
            ],
            'pages.accounts_tree.filters.expenses' => [
                'en' => 'Expenses',
                'ar' => 'المصروفات',
            ],
            'pages.accounts_tree.actions.export_excel' => [
                'en' => 'Export Excel',
                'ar' => 'تصدير Excel',
            ],
            'pages.accounts_tree.actions.reset' => [
                'en' => 'Reset',
                'ar' => 'إعادة تعيين',
            ],
            'pages.accounts_tree.actions.add_account' => [
                'en' => 'Add Account',
                'ar' => 'إضافة حساب',
            ],
            'pages.accounts_tree.search.placeholder' => [
                'en' => 'Search accounts...',
                'ar' => 'بحث عن الحسابات...',
            ],
            'pages.accounts_tree.empty_state.no_accounts' => [
                'en' => 'No accounts found',
                'ar' => 'لا توجد حسابات',
            ],
            'pages.accounts_tree.empty_state.get_started' => [
                'en' => 'Get started by creating a new account.',
                'ar' => 'ابدأ بإنشاء حساب جديد.',
            ],
            'pages.accounts_tree.account_details.title' => [
                'en' => 'Account Details',
                'ar' => 'تفاصيل الحساب',
            ],
            'pages.accounts_tree.account_details.no_selected' => [
                'en' => 'No Account Selected',
                'ar' => 'لم يتم تحديد حساب',
            ],
            'pages.accounts_tree.account_details.click_to_view' => [
                'en' => 'Click on an account from the tree to view its details',
                'ar' => 'انقر على حساب من الشجرة لعرض تفاصيله',
            ],
            'pages.accounts_tree.account_details.account_code' => [
                'en' => 'Account Code',
                'ar' => 'رمز الحساب',
            ],
            'pages.accounts_tree.account_details.account_name' => [
                'en' => 'Account Name',
                'ar' => 'اسم الحساب',
            ],
            'pages.accounts_tree.account_details.parent_account' => [
                'en' => 'Parent Account',
                'ar' => 'الحساب الأب',
            ],
            'pages.accounts_tree.account_details.account_type' => [
                'en' => 'Account Type',
                'ar' => 'نوع الحساب',
            ],
            'pages.accounts_tree.account_details.account_level' => [
                'en' => 'Account Level',
                'ar' => 'مستوى الحساب',
            ],
            'pages.accounts_tree.account_details.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'pages.accounts_tree.account_details.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'pages.accounts_tree.account_details.inactive' => [
                'en' => 'Inactive',
                'ar' => 'غير نشط',
            ],
            'pages.accounts_tree.account_details.allow_manual_entry' => [
                'en' => 'Allow Manual Entry',
                'ar' => 'السماح بالإدخال اليدوي',
            ],
            'pages.accounts_tree.account_details.notes' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            'pages.accounts_tree.account_details.child_accounts' => [
                'en' => 'Child Accounts',
                'ar' => 'الحسابات الفرعية',
            ],
            'pages.accounts_tree.account_details.sub_accounts' => [
                'en' => 'sub-account(s)',
                'ar' => 'حساب فرعي',
            ],
            'pages.accounts_tree.actions.edit_account' => [
                'en' => 'Edit Account',
                'ar' => 'تعديل الحساب',
            ],
            'pages.accounts_tree.actions.add_sub_account' => [
                'en' => 'Add Sub Account',
                'ar' => 'إضافة حساب فرعي',
            ],
            'pages.accounts_tree.actions.delete_account' => [
                'en' => 'Delete Account',
                'ar' => 'حذف الحساب',
            ],
            'pages.accounts_tree.modal.edit_title' => [
                'en' => 'Edit Account',
                'ar' => 'تعديل الحساب',
            ],
            'pages.accounts_tree.modal.create_title' => [
                'en' => 'Create New Account',
                'ar' => 'إنشاء حساب جديد',
            ],
            'pages.accounts_tree.modal.edit_description' => [
                'en' => 'Update account information',
                'ar' => 'تحديث معلومات الحساب',
            ],
            'pages.accounts_tree.modal.create_description' => [
                'en' => 'Add a new account to your chart of accounts',
                'ar' => 'إضافة حساب جديد إلى دليل الحسابات',
            ],
            'pages.accounts_tree.form.account_code' => [
                'en' => 'Account Code *',
                'ar' => 'رمز الحساب *',
            ],
            'pages.accounts_tree.form.account_code_placeholder' => [
                'en' => 'e.g., 1000, 1100',
                'ar' => 'مثال: 1000، 1100',
            ],
            'pages.accounts_tree.form.account_name' => [
                'en' => 'Account Name *',
                'ar' => 'اسم الحساب *',
            ],
            'pages.accounts_tree.form.account_name_placeholder' => [
                'en' => 'Enter account name',
                'ar' => 'أدخل اسم الحساب',
            ],
            'pages.accounts_tree.form.account_type' => [
                'en' => 'Account Type *',
                'ar' => 'نوع الحساب *',
            ],
            'pages.accounts_tree.form.select_type' => [
                'en' => 'Select Type',
                'ar' => 'اختر النوع',
            ],
            'pages.accounts_tree.form.parent_account' => [
                'en' => 'Parent Account',
                'ar' => 'الحساب الأب',
            ],
            'pages.accounts_tree.form.none_root' => [
                'en' => 'None (Root Account)',
                'ar' => 'لا شيء (حساب جذر)',
            ],
            'pages.accounts_tree.form.level' => [
                'en' => 'Level (Auto-calculated)',
                'ar' => 'المستوى (محسوب تلقائيًا)',
            ],
            'pages.accounts_tree.form.is_active' => [
                'en' => 'Account is Active',
                'ar' => 'الحساب نشط',
            ],
            'pages.accounts_tree.form.allow_manual_entry' => [
                'en' => 'Allow Manual Entry',
                'ar' => 'السماح بالإدخال اليدوي',
            ],
            'pages.accounts_tree.form.notes' => [
                'en' => 'Notes (Optional)',
                'ar' => 'ملاحظات (اختياري)',
            ],
            'pages.accounts_tree.form.notes_placeholder' => [
                'en' => 'Add any additional notes or comments about this account...',
                'ar' => 'أضف أي ملاحظات أو تعليقات إضافية حول هذا الحساب...',
            ],
            'pages.accounts_tree.form.update_account' => [
                'en' => 'Update Account',
                'ar' => 'تحديث الحساب',
            ],
            'pages.accounts_tree.form.create_account' => [
                'en' => 'Create Account',
                'ar' => 'إنشاء الحساب',
            ],
            'pages.accounts_tree.messages.account_updated' => [
                'en' => 'Account updated successfully.',
                'ar' => 'تم تحديث الحساب بنجاح.',
            ],
            'pages.accounts_tree.messages.account_created' => [
                'en' => 'Account created successfully.',
                'ar' => 'تم إنشاء الحساب بنجاح.',
            ],
            'pages.accounts_tree.messages.account_deleted' => [
                'en' => 'Account deleted successfully.',
                'ar' => 'تم حذف الحساب بنجاح.',
            ],
            'pages.accounts_tree.messages.cannot_delete_with_children' => [
                'en' => 'Cannot delete account with child accounts. Please delete child accounts first.',
                'ar' => 'لا يمكن حذف الحساب الذي يحتوي على حسابات فرعية. يرجى حذف الحسابات الفرعية أولاً.',
            ],
            'pages.accounts_tree.messages.cannot_delete_with_transactions' => [
                'en' => 'Cannot delete account. This account has :count transaction(s) or journal entry line(s). Please remove all transactions first.',
                'ar' => 'لا يمكن حذف الحساب. يحتوي هذا الحساب على :count معاملة أو سطر قيد يومية. يرجى إزالة جميع المعاملات أولاً.',
            ],
            'pages.accounts_tree.confirm.delete' => [
                'en' => 'Are you sure you want to delete this account? This action cannot be undone.',
                'ar' => 'هل أنت متأكد من حذف هذا الحساب؟ لا يمكن التراجع عن هذا الإجراء.',
            ],
            'pages.accounts_tree.export.headers.code' => [
                'en' => 'Code',
                'ar' => 'الرمز',
            ],
            'pages.accounts_tree.export.headers.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'pages.accounts_tree.export.headers.type' => [
                'en' => 'Type',
                'ar' => 'النوع',
            ],
            'pages.accounts_tree.export.headers.parent_account' => [
                'en' => 'Parent Account',
                'ar' => 'الحساب الأب',
            ],
            'pages.accounts_tree.export.headers.level' => [
                'en' => 'Level',
                'ar' => 'المستوى',
            ],
            'pages.accounts_tree.export.headers.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'pages.accounts_tree.export.headers.allow_manual_entry' => [
                'en' => 'Allow Manual Entry',
                'ar' => 'السماح بالإدخال اليدوي',
            ],
            'pages.accounts_tree.export.headers.notes' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            'pages.accounts_tree.account_type.asset' => [
                'en' => 'Asset',
                'ar' => 'أصل',
            ],
            'pages.accounts_tree.account_type.liability' => [
                'en' => 'Liability',
                'ar' => 'التزام',
            ],
            'pages.accounts_tree.account_type.equity' => [
                'en' => 'Equity',
                'ar' => 'حقوق الملكية',
            ],
            'pages.accounts_tree.account_type.revenue' => [
                'en' => 'Revenue',
                'ar' => 'إيراد',
            ],
            'pages.accounts_tree.account_type.expense' => [
                'en' => 'Expense',
                'ar' => 'مصروف',
            ],
            'pages.accounts_tree.form.level_auto' => [
                'en' => 'Level (Auto-calculated)',
                'ar' => 'المستوى (محسوب تلقائياً)',
            ],
            'pages.accounts_tree.form.account_is_active' => [
                'en' => 'Account is Active',
                'ar' => 'الحساب نشط',
            ],
            'pages.accounts_tree.form.allow_manual_entry_label' => [
                'en' => 'Allow Manual Entry',
                'ar' => 'السماح بالإدخال اليدوي',
            ],
            'pages.accounts_tree.form.notes_optional' => [
                'en' => 'Notes (Optional)',
                'ar' => 'ملاحظات (اختياري)',
            ],
            'pages.accounts_tree.form.notes_placeholder' => [
                'en' => 'Add any additional notes or comments about this account...',
                'ar' => 'أضف أي ملاحظات أو تعليقات إضافية حول هذا الحساب...',
            ],
            'pages.accounts_tree.tree_item.inactive' => [
                'en' => 'Inactive',
                'ar' => 'غير نشط',
            ],
            'pages.accounts_tree.tree_item.expand' => [
                'en' => 'Expand',
                'ar' => 'توسيع',
            ],
            'pages.accounts_tree.tree_item.collapse' => [
                'en' => 'Collapse',
                'ar' => 'طي',
            ],
            'pages.accounts_tree.tree_item.edit_account' => [
                'en' => 'Edit account',
                'ar' => 'تعديل الحساب',
            ],
            'pages.accounts_tree.tree_item.add_child_account' => [
                'en' => 'Add child account',
                'ar' => 'إضافة حساب فرعي',
            ],
            'pages.accounts_tree.export.yes' => [
                'en' => 'Yes',
                'ar' => 'نعم',
            ],
            'pages.accounts_tree.export.no' => [
                'en' => 'No',
                'ar' => 'لا',
            ],

            // User Profile Page
            'pages.user_profile.title' => [
                'en' => 'My Profile',
                'ar' => 'ملفي الشخصي',
            ],
            'pages.user_profile.sections.language_theme' => [
                'en' => 'Language & Theme',
                'ar' => 'اللغة والمظهر',
            ],
            'pages.user_profile.sections.language_theme_description' => [
                'en' => 'Configure your preferred language and visual theme',
                'ar' => 'قم بتكوين لغتك المفضلة والمظهر المرئي',
            ],
            'pages.user_profile.sections.date_time_preferences' => [
                'en' => 'Date & Time Preferences',
                'ar' => 'تفضيلات التاريخ والوقت',
            ],
            'pages.user_profile.sections.date_time_preferences_description' => [
                'en' => 'Customize how dates and times are displayed',
                'ar' => 'قم بتخصيص طريقة عرض التواريخ والأوقات',
            ],
            'pages.user_profile.fields.language' => [
                'en' => 'Language',
                'ar' => 'اللغة',
            ],
            'pages.user_profile.fields.theme' => [
                'en' => 'Theme',
                'ar' => 'المظهر',
            ],
            'pages.user_profile.fields.timezone' => [
                'en' => 'Timezone',
                'ar' => 'المنطقة الزمنية',
            ],
            'pages.user_profile.fields.date_format' => [
                'en' => 'Date Format',
                'ar' => 'تنسيق التاريخ',
            ],
            'pages.user_profile.fields.time_format' => [
                'en' => 'Time Format',
                'ar' => 'تنسيق الوقت',
            ],
            'pages.user_profile.helpers.language' => [
                'en' => 'Select your preferred language for the dashboard',
                'ar' => 'اختر لغتك المفضلة للوحة التحكم',
            ],
            'pages.user_profile.helpers.theme' => [
                'en' => 'Choose a visual theme for your dashboard',
                'ar' => 'اختر مظهرًا مرئيًا للوحة التحكم',
            ],
            'pages.user_profile.helpers.timezone' => [
                'en' => 'Select your timezone',
                'ar' => 'اختر منطقتك الزمنية',
            ],
            'pages.user_profile.helpers.date_format' => [
                'en' => 'Choose how dates are displayed',
                'ar' => 'اختر طريقة عرض التواريخ',
            ],
            'pages.user_profile.helpers.time_format' => [
                'en' => 'Choose how times are displayed',
                'ar' => 'اختر طريقة عرض الأوقات',
            ],

            // Users Resource
            'forms.users.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'forms.users.email' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'forms.users.password' => [
                'en' => 'Password',
                'ar' => 'كلمة المرور',
            ],
            'forms.users.roles' => [
                'en' => 'Roles',
                'ar' => 'الأدوار',
            ],
            'tables.users.name' => [
                'en' => 'Name',
                'ar' => 'الاسم',
            ],
            'tables.users.email' => [
                'en' => 'Email',
                'ar' => 'البريد الإلكتروني',
            ],
            'tables.users.roles' => [
                'en' => 'Roles',
                'ar' => 'الأدوار',
            ],
            'tables.common.id' => [
                'en' => 'Id',
                'ar' => 'المعرف',
            ],

            // Roles Resource
            'forms.roles.name' => [
                'en' => 'Role Name',
                'ar' => 'اسم الدور',
            ],
            'forms.roles.guard' => [
                'en' => 'Guard',
                'ar' => 'الحارس',
            ],
            'forms.roles.permissions' => [
                'en' => 'Permissions',
                'ar' => 'الصلاحيات',
            ],
            'forms.roles.permissions_helper' => [
                'en' => 'Select permissions assigned to this role. You can select all permissions at once using the checkbox.',
                'ar' => 'حدد الصلاحيات المخصصة لهذا الدور. يمكنك تحديد جميع الصلاحيات مرة واحدة باستخدام مربع الاختيار.',
            ],
            'tables.roles.role' => [
                'en' => 'Role',
                'ar' => 'الدور',
            ],
            'tables.roles.guard' => [
                'en' => 'Guard',
                'ar' => 'الحارس',
            ],
            'tables.roles.permissions' => [
                'en' => 'Permissions',
                'ar' => 'الصلاحيات',
            ],

            // Permissions Resource
            'forms.permissions.name' => [
                'en' => 'Permission Name',
                'ar' => 'اسم الصلاحية',
            ],
            'forms.permissions.guard' => [
                'en' => 'Guard',
                'ar' => 'الحارس',
            ],
            'tables.permissions.permission' => [
                'en' => 'Permission',
                'ar' => 'الصلاحية',
            ],
            'tables.permissions.guard' => [
                'en' => 'Guard',
                'ar' => 'الحارس',
            ],

            // Export Actions
            'actions.export_to_excel' => [
                'en' => 'Export to Excel',
                'ar' => 'تصدير إلى Excel',
            ],
            'actions.export_to_pdf' => [
                'en' => 'Export to PDF',
                'ar' => 'تصدير إلى PDF',
            ],

            // Bank Guarantees Resource - Navigation
            'sidebar.accounting.bank_guarantees' => [
                'en' => 'Bank Guarantees',
                'ar' => 'خطابات الضمان البنكي',
            ],
            'navigation.bank_guarantees' => [
                'en' => 'Bank Guarantees',
                'ar' => 'خطابات الضمان البنكي',
            ],

            // Bank Guarantees Resource - Forms
            'forms.bank_guarantees.sections.basic_information' => [
                'en' => 'Basic Information',
                'ar' => 'المعلومات الأساسية',
            ],
            'forms.bank_guarantees.sections.financial_information' => [
                'en' => 'Financial Information',
                'ar' => 'المعلومات المالية',
            ],
            'forms.bank_guarantees.sections.attachments' => [
                'en' => 'Attachments',
                'ar' => 'المرفقات',
            ],
            'forms.bank_guarantees.sections.notes' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات',
            ],
            'forms.bank_guarantees.fields.guarantee_number' => [
                'en' => 'Guarantee Number',
                'ar' => 'رقم خطاب الضمان',
            ],
            'forms.bank_guarantees.fields.beneficiary_name' => [
                'en' => 'Beneficiary Name',
                'ar' => 'اسم الجهة المستفيدة',
            ],
            'forms.bank_guarantees.fields.issue_date' => [
                'en' => 'Issue Date',
                'ar' => 'تاريخ الإصدار',
            ],
            'forms.bank_guarantees.fields.start_date' => [
                'en' => 'Start Date',
                'ar' => 'تاريخ البدء',
            ],
            'forms.bank_guarantees.fields.end_date' => [
                'en' => 'End Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'forms.bank_guarantees.fields.current_end_date' => [
                'en' => 'Current End Date',
                'ar' => 'تاريخ الانتهاء الحالي',
            ],
            'forms.bank_guarantees.fields.new_end_date' => [
                'en' => 'New End Date',
                'ar' => 'تاريخ الانتهاء الجديد',
            ],
            'forms.bank_guarantees.fields.new_end_date_helper' => [
                'en' => 'The new end date must be after the current end date',
                'ar' => 'يجب أن يكون تاريخ الانتهاء الجديد بعد تاريخ الانتهاء الحالي',
            ],
            'forms.bank_guarantees.fields.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'forms.bank_guarantees.fields.exchange_rate' => [
                'en' => 'Exchange Rate',
                'ar' => 'سعر الصرف',
            ],
            'forms.bank_guarantees.fields.exchange_rate_helper' => [
                'en' => 'Automatically fetched from currency rates based on issue date',
                'ar' => 'يتم جلب سعر الصرف تلقائياً من جدول أسعار العملات بناءً على تاريخ الإصدار',
            ],
            'forms.bank_guarantees.fields.base_amount' => [
                'en' => 'Base Amount (in default currency)',
                'ar' => 'المبلغ الأساسي (بالعملة الافتراضية)',
            ],
            'forms.bank_guarantees.fields.amount' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'forms.bank_guarantees.fields.bank_fees' => [
                'en' => 'Bank Fees',
                'ar' => 'المصروفات البنكية',
            ],
            'forms.bank_guarantees.fields.original_guarantee_account' => [
                'en' => 'Original Guarantee Account (Debit)',
                'ar' => 'الحساب المدين (أصل خطاب الضمان)',
            ],
            'forms.bank_guarantees.fields.bank_account' => [
                'en' => 'Bank Account (Credit)',
                'ar' => 'الحساب الدائن (حساب البنك/الصندوق)',
            ],
            'forms.bank_guarantees.fields.bank_fees_account' => [
                'en' => 'Bank Fees Account',
                'ar' => 'حساب المصروفات البنكية',
            ],
            'forms.bank_guarantees.fields.bank_fees_debit_account' => [
                'en' => 'Bank Fees Debit Account',
                'ar' => 'الحساب المدين للمصروفات البنكية',
            ],
            'forms.bank_guarantees.fields.attachment' => [
                'en' => 'Attachment',
                'ar' => 'مرفق خطاب الضمان',
            ],
            'forms.bank_guarantees.fields.notes' => [
                'en' => 'Notes',
                'ar' => 'ملاحظات إضافية',
            ],
            'forms.bank_guarantees.fields.branch' => [
                'en' => 'Branch',
                'ar' => 'الفرع',
            ],
            'forms.bank_guarantees.fields.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'forms.bank_guarantees.status.active' => [
                'en' => 'Active',
                'ar' => 'نشط',
            ],
            'forms.bank_guarantees.status.expired' => [
                'en' => 'Expired',
                'ar' => 'منتهي',
            ],
            'forms.bank_guarantees.status.cancelled' => [
                'en' => 'Cancelled',
                'ar' => 'ملغي',
            ],
            'forms.bank_guarantees.currency_symbol' => [
                'en' => 'SAR',
                'ar' => 'ريال',
            ],

            // Bank Guarantees Resource - Tables
            'tables.bank_guarantees.guarantee_number' => [
                'en' => 'Guarantee Number',
                'ar' => 'رقم خطاب الضمان',
            ],
            'tables.bank_guarantees.beneficiary_name' => [
                'en' => 'Beneficiary Name',
                'ar' => 'اسم الجهة المستفيدة',
            ],
            'tables.bank_guarantees.amount' => [
                'en' => 'Amount',
                'ar' => 'المبلغ',
            ],
            'tables.bank_guarantees.currency' => [
                'en' => 'Currency',
                'ar' => 'العملة',
            ],
            'tables.bank_guarantees.end_date' => [
                'en' => 'End Date',
                'ar' => 'تاريخ الانتهاء',
            ],
            'tables.bank_guarantees.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.bank_guarantees.created_at' => [
                'en' => 'Created At',
                'ar' => 'تاريخ الإنشاء',
            ],
            'tables.bank_guarantees.filters.status' => [
                'en' => 'Status',
                'ar' => 'الحالة',
            ],
            'tables.bank_guarantees.filters.end_date_from' => [
                'en' => 'End Date From',
                'ar' => 'تاريخ الانتهاء من',
            ],
            'tables.bank_guarantees.filters.end_date_until' => [
                'en' => 'End Date Until',
                'ar' => 'تاريخ الانتهاء حتى',
            ],
            'tables.bank_guarantees.filters.expired_soon' => [
                'en' => 'Expiring Soon (within 30 days)',
                'ar' => 'تنتهي قريباً (خلال 30 يوم)',
            ],
            'tables.bank_guarantees.filters.beneficiary_name' => [
                'en' => 'Beneficiary Name',
                'ar' => 'اسم الجهة المستفيدة',
            ],

            // Bank Guarantees - Actions
            'actions.renew' => [
                'en' => 'Renew',
                'ar' => 'تمديد',
            ],
            'actions.renew_description' => [
                'en' => 'Extend the expiry date of this bank guarantee',
                'ar' => 'تمديد تاريخ انتهاء خطاب الضمان البنكي',
            ],
            'actions.save_renewal' => [
                'en' => 'Save Renewal',
                'ar' => 'حفظ التمديد',
            ],

            // Bank Guarantees - Messages
            'messages.renewed_successfully' => [
                'en' => 'Renewed Successfully',
                'ar' => 'تم التمديد بنجاح',
            ],
            'messages.bank_guarantee_renewed' => [
                'en' => 'Bank guarantee renewed from :old_date to :new_date',
                'ar' => 'تم تمديد خطاب الضمان البنكي من :old_date إلى :new_date',
            ],
            'messages.renewal_failed' => [
                'en' => 'Renewal Failed',
                'ar' => 'فشل التمديد',
            ],
            'messages.bank_guarantees.created' => [
                'en' => 'Bank Guarantee Created',
                'ar' => 'تم إنشاء خطاب الضمان',
            ],
            'messages.bank_guarantees.created_successfully' => [
                'en' => 'Bank guarantee has been created successfully',
                'ar' => 'تم إنشاء خطاب الضمان البنكي بنجاح',
            ],
            'messages.bank_guarantees.updated' => [
                'en' => 'Bank Guarantee Updated',
                'ar' => 'تم تحديث خطاب الضمان',
            ],
            'messages.bank_guarantees.updated_successfully' => [
                'en' => 'Bank guarantee has been updated successfully',
                'ar' => 'تم تحديث خطاب الضمان البنكي بنجاح',
            ],

            // Bank Guarantees - Helpers
            'helpers.auto_generate_number' => [
                'en' => 'Leave this field empty to auto-generate a guarantee number',
                'ar' => 'اترك الحقل فارغاً ليتم توليد رقم تلقائياً',
            ],
            'helpers.fees_zero_if_none' => [
                'en' => 'Leave the value as zero if there are no bank fees',
                'ar' => 'اترك القيمة صفر إذا لم توجد مصروفات بنكية',
            ],
            'helpers.fees_debit_optional' => [
                'en' => 'Optional: If left empty, the bank account will be used',
                'ar' => 'اختياري: إذا تركت فارغاً، سيتم استخدام حساب البنك',
            ],

            // Bank Guarantees - Validation
            'validation.renewal_date_must_be_after' => [
                'en' => 'The new end date must be after the current end date',
                'ar' => 'يجب أن يكون تاريخ الانتهاء الجديد بعد تاريخ الانتهاء الحالي',
            ],
            'validation.bank_fees_account_required' => [
                'en' => 'Bank fees account is required when bank fees are greater than zero',
                'ar' => 'حساب المصروفات البنكية مطلوب عندما تكون المصروفات البنكية أكبر من الصفر',
            ],
            'validation.end_date_after_start_date' => [
                'en' => 'End date must be after start date',
                'ar' => 'يجب أن يكون تاريخ الانتهاء بعد تاريخ البدء',
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

