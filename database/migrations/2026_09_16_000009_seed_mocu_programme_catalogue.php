<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $deptCache = [];
    protected array $levelCache = [];

    public function up(): void
    {
        // ---- Ensure Kizumbi campus exists ----
        $campusMain = DB::table('campuses')->where('name', 'like', '%Main%')->value('id') ?? (DB::table('campuses')->value('id'));
        $campusKiz  = DB::table('campuses')->where('name', 'like', '%Kizumbi%')->value('id');
        if (!$campusKiz) {
            $campusKiz = DB::table('campuses')->insertGetId([
                'name' => 'Kizumbi Institute of Co-operative and Business Education',
                'code' => 'KIZ',
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $levels = DB::table('admission_levels')->pluck('id', 'name');

        $rows = [
            // ---------------- CERTIFICATE — Main Campus (1 Year) ----------------
            ['code'=>'CLIS','name'=>'Certificate in Library and Information Sciences','levelName'=>'Certificate','dept'=>'Library and Information Science','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CAF','name'=>'Certificate in Accounting and Finance','levelName'=>'Certificate','dept'=>'Accounting','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CMFM','name'=>'Certificate in Microfinance Management','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CIT','name'=>'Certificate in Information Technology','levelName'=>'Certificate','dept'=>'Information Systems','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CLAW','name'=>'Certificate in Law','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CHRM','name'=>'Certificate in Human Resource Management','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CCMA','name'=>'Certificate in Co-operative Management and Accounting','levelName'=>'Certificate','dept'=>'Accounting','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'CCQT','name'=>'Certificate in Coffee Quality and Trade','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],

            // ---------------- DIPLOMA — Main Campus (2 Years) ----------------
            ['code'=>'DLIS','name'=>'Diploma in Library and Information Sciences','levelName'=>'Diploma','dept'=>'Library and Information Science','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DMM','name'=>'Diploma in Microfinance Management','levelName'=>'Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DBICT','name'=>'Diploma in Business Information and Communication Technology','levelName'=>'Diploma','dept'=>'Information Systems','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DHRM','name'=>'Diploma in Human Resource Management','levelName'=>'Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DBEM','name'=>'Diploma in Business and Enterprise Management','levelName'=>'Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DCMA','name'=>'Diploma in Co-operative Management and Accounting','levelName'=>'Diploma','dept'=>'Accounting','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'DMMD','name'=>'Diploma in Microfinance Management — Distance','levelName'=>'Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Distance'],

            // ---------------- BACHELOR — Main Campus (3 Years unless noted) ----------------
            ['code'=>'BLIS','name'=>'Bachelor of Library and Information Science','levelName'=>'Bachelor','dept'=>'Library and Information Science','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAF','name'=>'Bachelor of Accounting and Finance','levelName'=>'Bachelor','dept'=>'Accounting','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAT','name'=>'Bachelor of Accounting and Taxation','levelName'=>'Bachelor','dept'=>'Accounting','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BBMF','name'=>'Bachelor of Banking and Microfinance','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BBICT','name'=>'Bachelor of Business Information and Communication Technology','levelName'=>'Bachelor','dept'=>'Information Systems','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSCS','name'=>'Bachelor of Science in Computer Science','levelName'=>'Bachelor','dept'=>'Computer Science','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSDS','name'=>'Bachelor of Science in Data Science','levelName'=>'Bachelor','dept'=>'Computer Science','campus'=>$campusMain,'years'=>4,'mode'=>'Full Time'],
            ['code'=>'LLB','name'=>'Bachelor of Laws','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BHRM','name'=>'Bachelor of Human Resource Management','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BEEM','name'=>'Bachelor of Entrepreneurship and Enterprise Management','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BMM','name'=>'Bachelor of Marketing Management','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BPSCM','name'=>'Bachelor of Procurement and Supply Chain Management','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAccCM','name'=>'Bachelor of Accounting with Co-operative Management','levelName'=>'Bachelor','dept'=>'Accounting','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BAccCME','name'=>'Bachelor of Accounting with Co-operative Management — Evening','levelName'=>'Bachelor','dept'=>'Accounting','campus'=>$campusMain,'years'=>3,'mode'=>'Evening'],
            ['code'=>'BCM','name'=>'Bachelor of Co-operative Management','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BCED','name'=>'Bachelor of Community Economic Development','levelName'=>'Bachelor','dept'=>'Economics','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSW','name'=>'Bachelor of Social Work','levelName'=>'Bachelor','dept'=>'Educational Psychology','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BEC','name'=>'Bachelor of Economics','levelName'=>'Bachelor','dept'=>'Economics','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BSA','name'=>'Bachelor of Statistics and Analytics','levelName'=>'Bachelor','dept'=>'Mathematics','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],

            // New 2026/27 Bachelor programmes
            ['code'=>'BBAIS','name'=>'Bachelor of Business Administration with Information Systems','levelName'=>'Bachelor','dept'=>'Information Systems','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'BCMA','name'=>'Bachelor of Co-operative Management with Agriculture','levelName'=>'Bachelor','dept'=>'Business Administration','campus'=>$campusMain,'years'=>3,'mode'=>'Full Time'],

            // ---------------- KIZUMBI — Certificate (1 Year) ----------------
            ['code'=>'KDCED','name'=>'Certificate in Enterprise Development','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusKiz,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'KDHRM','name'=>'Certificate in Human Resource Management','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusKiz,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'KDMM','name'=>'Certificate in Microfinance Management','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusKiz,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'KDCMA','name'=>'Certificate in Co-operative Management and Accounting','levelName'=>'Certificate','dept'=>'Business Administration','campus'=>$campusKiz,'years'=>1,'mode'=>'Full Time'],

            // ---------------- KIZUMBI — Diploma (2 Years) ----------------
            ['code'=>'KDDMM','name'=>'Diploma in Microfinance Management','levelName'=>'Diploma','dept'=>'Business Administration','campus'=>$campusKiz,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'KDDCMA','name'=>'Diploma in Co-operative Management and Accounting','levelName'=>'Diploma','dept'=>'Business Administration','campus'=>$campusKiz,'years'=>2,'mode'=>'Full Time'],

            // ---------------- KIZUMBI — Bachelor (3 Years) ----------------
            ['code'=>'KBBBA','name'=>'Bachelor of Business Administration with Information Systems','levelName'=>'Bachelor','dept'=>'Information Systems','campus'=>$campusKiz,'years'=>3,'mode'=>'Full Time'],
            ['code'=>'KBBACM','name'=>'Bachelor of Accounting with Co-operative Management','levelName'=>'Bachelor','dept'=>'Accounting','campus'=>$campusKiz,'years'=>3,'mode'=>'Full Time'],

            // ---------------- POSTGRADUATE DIPLOMA (1 Year) ----------------
            ['code'=>'PGDAF-E','name'=>'Postgraduate Diploma in Accounting and Finance — Evening','levelName'=>'Postgraduate Diploma','dept'=>'Accounting','campus'=>$campusMain,'years'=>1,'mode'=>'Evening'],
            ['code'=>'PGDAF-F','name'=>'Postgraduate Diploma in Accounting and Finance — Full Time','levelName'=>'Postgraduate Diploma','dept'=>'Accounting','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDSCC-F','name'=>'Postgraduate Diploma in Savings and Credit Co-operatives Societies Management — Full Time','levelName'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDCBM-F','name'=>'Postgraduate Diploma in Co-operative Business Management — Full Time','levelName'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDCBM-E','name'=>'Postgraduate Diploma in Co-operative Business Management — Evening','levelName'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Evening'],
            ['code'=>'PGDCD-E','name'=>'Postgraduate Diploma in Community Development — Evening','levelName'=>'Postgraduate Diploma','dept'=>'Educational Psychology','campus'=>$campusMain,'years'=>1,'mode'=>'Evening'],
            ['code'=>'PGDCD-F','name'=>'Postgraduate Diploma in Community Development — Full Time','levelName'=>'Postgraduate Diploma','dept'=>'Educational Psychology','campus'=>$campusMain,'years'=>1,'mode'=>'Full Time'],
            ['code'=>'PGDSCC-D','name'=>'Postgraduate Diploma in Savings and Credit Co-operatives Societies Management — Distance','levelName'=>'Postgraduate Diploma','dept'=>'Business Administration','campus'=>$campusMain,'years'=>1,'mode'=>'Distance'],

            // ---------------- MASTERS (2 Years unless noted) ----------------
            ['code'=>'MBF','name'=>'Master of Banking and Financial Services','levelName'=>'Masters','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MBM-E','name'=>'Master of Business Management — Evening','levelName'=>'Masters','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Evening'],
            ['code'=>'MBM-F','name'=>'Master of Business Management — Full Time','levelName'=>'Masters','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MPSM-F','name'=>'Master of Arts in Procurement and Supply Management — Full Time','levelName'=>'Masters','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MACD-E','name'=>'Master of Arts in Co-operative and Community Development — Evening','levelName'=>'Masters','dept'=>'Economics','campus'=>$campusMain,'years'=>2,'mode'=>'Evening'],
            ['code'=>'MACD-F','name'=>'Master of Arts in Co-operative and Community Development — Full Time','levelName'=>'Masters','dept'=>'Economics','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MCM','name'=>'Master of Co-operative Management','levelName'=>'Masters','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MDP','name'=>'Master in Development Planning','levelName'=>'Masters','dept'=>'Economics','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MCommDev-E','name'=>'Master of Community Development — Evening','levelName'=>'Masters','dept'=>'Educational Psychology','campus'=>$campusMain,'years'=>3,'mode'=>'Evening'],
            ['code'=>'MCommDev-F','name'=>'Master of Community Development — Full Time','levelName'=>'Masters','dept'=>'Educational Psychology','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],
            ['code'=>'MPPM','name'=>'Master of Project Planning and Management','levelName'=>'Masters','dept'=>'Business Administration','campus'=>$campusMain,'years'=>2,'mode'=>'Full Time'],

            // ---------------- PHD ----------------
            ['code'=>'PHD','name'=>'Doctor of Philosophy','levelName'=>'PhD','dept'=>'Business Administration','campus'=>$campusMain,'years'=>4,'mode'=>'Full Time'],
        ];

        // Per-level default tuition (TZS) so public catalogue shows fees
        $tuition = ['Certificate'=>900000,'Diploma'=>1_300_000,'Bachelor'=>1_800_000,'Postgraduate Diploma'=>2_500_000,'Masters'=>3_500_000,'PhD'=>4_500_000];

        $inserted = 0; $skipped = 0;
        foreach ($rows as $r) {
            $exists = DB::table('programmes')->where('code', $r['code'])->exists();
            if ($exists) { $skipped++; continue; }
            $levelId = $this->levelCache($r['levelName'], $levels);
            $deptId  = $this->dept($r['dept']);
            DB::table('programmes')->insert([
                'department_id'      => $deptId,
                'campus_id'          => $r['campus'],
                'admission_level_id' => $levelId,
                'name'               => $r['name'],
                'code'               => $r['code'],
                'duration_years'     => $r['years'],
                'study_mode'         => $r['mode'],
                'tuition_fee'        => $tuition[$r['levelName']] ?? 0,
                'status'             => 'active',
                'created_at' => now(), 'updated_at' => now(),
            ]);
            $inserted++;
        }

        echo "MoCU catalogue seeded: {$inserted} added, {$skipped} already present.\n";
    }

    protected function levelCache(string $name, $levels): int
    {
        if (isset($this->levelCache[$name])) return $this->levelCache[$name];
        $id = $levels->get($name);
        if (!$id) throw new \RuntimeException("Admission level not found: {$name}");
        return $this->levelCache[$name] = $id;
    }

    protected function dept(string $wanted): int
    {
        if (isset($this->deptCache[$wanted])) return $this->deptCache[$wanted];

        $id = DB::table('departments')->where('name', $wanted)->value('id');
        if (!$id) {
            // Map to closest existing department by keyword, else create under faculty 4 (Business)
            $likes = ['Library'=>'Library','Information'=>'Information Systems','Accounting'=>'Accounting','Economics'=>'Economics','Mathematics'=>'Mathematics','Business'=>'Business Administration','Computer'=>'Computer Science','Psychology'=>'Educational Psychology'];
            $match = null;
            foreach ($likes as $kw => $dept) { if (str_contains($wanted, $kw)) { $match = $dept; break; } }
            if (!$match) $match = 'Business Administration';
            $id = DB::table('departments')->where('name', $match)->value('id');
            if (!$id) {
                $facultyId = DB::table('faculties')->where('name', 'like', '%Business%')->value('id') ?? DB::table('faculties')->value('id');
                $id = DB::table('departments')->insertGetId(['faculty_id'=>$facultyId,'name'=>$wanted,'created_at'=>now(),'updated_at'=>now()]);
            }
        }
        return $this->deptCache[$wanted] = $id;
    }

    public function down(): void
    {
        // Best-effort: only remove programmes created with the catalogue codes.
        $codes = ['CLIS','CAF','CMFM','CIT','CLAW','CHRM','CCMA','CCQT','DLIS','DMM','DBICT','DHRM','DBEM','DCMA','DMMD','BLIS','BAF','BAT','BBMF','BBICT','BSCS','BSDS','LLB','BHRM','BEEM','BMM','BPSCM','BAccCM','BAccCME','BCM','BCED','BSW','BEC','BSA','BBAIS','BCMA','KDCED','KDHRM','KDMM','KDCMA','KDDMM','KDDCMA','KBBBA','KBBACM','PGDAF-E','PGDAF-F','PGDSCC-F','PGDCBM-F','PGDCBM-E','PGDCD-E','PGDCD-F','PGDSCC-D','MBF','MBM-E','MBM-F','MPSM-F','MACD-E','MACD-F','MCM','MDP','MCommDev-E','MCommDev-F','MPPM','PHD'];
        DB::table('programmes')->whereIn('code', $codes)->delete();
    }
};
