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

