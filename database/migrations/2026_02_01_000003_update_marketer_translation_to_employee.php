<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $arabicLanguage = DB::table('languages')->where('code', 'ar')->first();
        
        if ($arabicLanguage) {
            DB::table('translations')
                ->where('key', 'recruitment_contract.fields.marketer')
                ->where('group', 'dashboard')
                ->where('language_id', $arabicLanguage->id)
                ->update(['value' => 'اسم الموظف']);
        }

        $englishLanguage = DB::table('languages')->where('code', 'en')->first();
        
        if ($englishLanguage) {
            DB::table('translations')
                ->where('key', 'recruitment_contract.fields.marketer')
                ->where('group', 'dashboard')
                ->where('language_id', $englishLanguage->id)
                ->update(['value' => 'Employee']);
        }
    }

    public function down(): void
    {
        $arabicLanguage = DB::table('languages')->where('code', 'ar')->first();
        
        if ($arabicLanguage) {
            DB::table('translations')
                ->where('key', 'recruitment_contract.fields.marketer')
                ->where('group', 'dashboard')
                ->where('language_id', $arabicLanguage->id)
                ->update(['value' => 'اسم المسوق']);
        }

        $englishLanguage = DB::table('languages')->where('code', 'en')->first();
        
        if ($englishLanguage) {
            DB::table('translations')
                ->where('key', 'recruitment_contract.fields.marketer')
                ->where('group', 'dashboard')
                ->where('language_id', $englishLanguage->id)
                ->update(['value' => 'Marketer']);
        }
    }
};
