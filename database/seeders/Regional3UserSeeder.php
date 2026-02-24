<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class Regional3UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('tiket-ktu3');

        $departments = [
            'Commercial' => Department::firstOrCreate(['name' => 'Commercial'])->id,
            'Dimensional' => Department::firstOrCreate(['name' => 'Dimensional'])->id,
            'Engineering' => Department::firstOrCreate(['name' => 'Engineering'])->id,
            'Facility' => Department::firstOrCreate(['name' => 'Facility'])->id,
            'HR' => Department::firstOrCreate(['name' => 'HR'])->id,
            'HSE' => Department::firstOrCreate(['name' => 'HSE'])->id,
            'ISO' => Department::firstOrCreate(['name' => 'ISO'])->id,
            'IT' => Department::firstOrCreate(['name' => 'IT'])->id,
            'M&L' => Department::firstOrCreate(['name' => 'Material & Logistic'])->id,
            'Planner' => Department::firstOrCreate(['name' => 'Planner'])->id,
            'PPC' => Department::firstOrCreate(['name' => 'PPIC'])->id,
            'Purchasing' => Department::firstOrCreate(['name' => 'Purchasing'])->id,
            'QA/QC' => Department::firstOrCreate(['name' => 'Quality Control'])->id,
            'Production PMT' => Department::firstOrCreate(['name' => 'Production PMT'])->id,
            'Project Management' => Department::firstOrCreate(['name' => 'Project Management'])->id,
            'Quality Control' => Department::firstOrCreate(['name' => 'Quality Control'])->id,
            'Warehouse' => Department::firstOrCreate(['name' => 'Warehouse'])->id,
        ];

        $users = [
            // TANJUNG RIAU (Regional 3 - Location 7)
            ['Jedi', '104192462', '7', 'Jedi', 'Commercial'],
            ['Ismail', '104233488', '7', 'Ismail', 'Commercial'],
            ['Lukman Affandy', '104244439', '7', 'Lukman', 'Commercial'],
            ['Rezkiyanti', '104223240', '7', 'Rezkiyanti', 'Commercial'],
            ['Zulfitriyani Yusuf', '104244190', '7', 'Zulfitriyani', 'Commercial'],
            ['M. Nur Rochman', '104223232', '7', 'Nurrochman', 'Dimensional'],
            ['Amira Ulfa', '104254825', '7', 'Amira', 'Engineering'],
            ['Usnadi Bi Burhan', '104222950', '7', 'Usnadi', 'Engineering'],
            ['Farras Abiyyu Raihan', '104244550', '7', 'Farras', 'Engineering'],
            ['Kib Angga Royani', '104212741', '7', 'Kibangga', 'Engineering'],
            ['Fietry Nadia', '104202658', '7', 'Fietry', 'Engineering'],
            ['Angga Eka Ap', '104202706', '7', 'Anggaeka', 'Engineering'],
            ['Caroline Maarja M.', '104223204', '7', 'Caroline', 'Engineering'],
            ['Fanny Ester D.S', '104254919', '7', 'Fanny', 'Engineering'],
            ['Faradilla Ramadani', '104244519', '7', 'Faradilla', 'Engineering'],
            ['Esa Ahmed A.K.A', '104192430', '7', 'Esaahmed', 'Engineering'],
            ['Albertus Calvin Pratama', '104254909', '7', 'Albertus', 'Engineering'],
            ['Wardatus Safira', '104254908', '7', 'Wardatus', 'Engineering'],
            ['Id-Idil Adha', '104233515', '7', 'Idiladha', 'Engineering'],
            ['Bagus Ezi P', '104254904', '7', 'Bagus', 'Engineering'],
            ['Tuti Rachmawati Yusuf', '104192488', '7', 'Tuti', 'Engineering'],
            ['Amiluddin', '104223700', '7', 'Amiluddin', 'Engineering'],
            ['Indah Wulandari Sri Rejeki', '104254561', '7', 'Indah', 'Engineering'],
            ['Yulianti', '104100009', '7', 'Yulianti', 'Engineering'],
            ['Yulyanno Constantino P.', '104212741', '7', 'Yulyanno', 'Engineering'],
            ['Syaiful Huda', '104212848', '7', 'Syaifulhuda', 'Engineering'],
            ['Moh Rozihan Fikri Al Afa', '104254939', '7', 'Rozihan', 'Engineering'],
            ['M Hasymal Dhurriatali', '104254829', '7', 'Hasymal', 'Engineering'],
            ['Raslan Syarifuddin', '102202679', '7', 'Raslan', 'Engineering'],
            ['Akhmad Subkhan', '104223375', '7', 'Akhmadsubkhan', 'Engineering'],
            ['Verdian Ridhoharijani Afham', '104155542', '7', 'Verdian', 'Engineering'],
            ['Nadya Paramita L.S', '104233609', '7', 'Nadyaparamita', 'Engineering'],
            ['Ilham', '104254679', '7', 'Ilham', 'Engineering'],
            ['Wahyu Pardana', '104212737', '7', 'Wahyu', 'Engineering'],
            ['Iqbal Hilmi Ridwan', '104254637', '7', 'Iqbal', 'Engineering'],
            ['Wawan Dwi Tanuwianto', '104254630', '7', 'Wawan', 'Engineering'],
            ['Olivia Regina Pramesti', '104212841', '7', 'Olivia', 'Engineering'],
            ['Hendra Prasetya', '104192453', '7', 'Hendraprasetya', 'Engineering'],
            ['Dedi Elfandi', '102251025', '7', 'Dedi', 'Facility'],
            ['Vitri', '104244062', '7', 'Vitri', 'Facility'],
            ['Imam Fudoli', '104222616', '7', 'Imam', 'Facility'],
            ['Harris Agus Risdianto', '104122176', '7', 'Harris', 'Facility'],
            ['Agi Septian', '104202534', '7', 'Agi', 'Facility'],
            ['Imron', '104233405', '7', 'Imron', 'HR'],
            ['Ismi Rufika', '104102694', '7', 'Ismi', 'HR'],
            ['Suara Marna Purba', '104103007', '7', 'Suara', 'HR'],
            ['Tidun Simanjuntak', '104103322', '7', 'Tidun', 'HR'],
            ['Franklyn Willy L.', '104202659', '7', 'Franklyn', 'HR'],
            ['Jappi Gewang', '104102695', '7', 'Jappi', 'HSE'],
            ['Anwari Umar', '104102435', '7', 'Anwari', 'HSE'],
            ['Warastiane Matine S', '104102434', '7', 'Matine', 'HSE'],
            ['Boy Zamanie', '104254611', '7', 'Boyzamanie', 'HSE'],
            ['Tidola Lianty Zefanya', '1033377138', '7', 'Tidola', 'ISO'],
            ['Boby Nainggolan', '101234433', '7', 'Bobynainggolan', 'IT'],
            ['Marini', '104212738', '7', 'Marini', 'M&L'],
            ['Chairuddin', '104233655', '7', 'Chairuddin', 'M&L'],
            ['Amby Too', '104233732', '7', 'Ambytoo', 'M&L'],
            ['Syelma Pertiwi', '103254922', '7', 'Syelma', 'M&L'],
            ['Ramah Hadianto', '103254921', '7', 'Ramadhadianto', 'M&L'],
            ['Okisani Devianti S.', '104222045', '7', 'Okisani', 'M&L'],
            ['Budi Seku Gunawan', '104222046', '7', 'Budiseku', 'M&L'],
            ['Debby Meyyani Sinuh', '103212803', '7', 'Debby', 'M&L'],
            ['Dalid', '104102447', '7', 'Dalid', 'M&L'],
            ['Ahmad Syahrony', '104223205', '7', 'Ahmadsyahrony', 'M&L'],
            ['Wahyu Blai', '101233635', '7', 'Wahyublai', 'M&L'],
            ['Ahmed Riyanto', '104212813', '7', 'Ahmedriyanto', 'Planner'],
            ['M. Syarafiy Mazhabibar', '103223126', '7', 'Syarafiy', 'PPC'],
            ['Hemlan', '104152307', '7', 'Hemlan', 'PPC'],
            ['Evi Yuniarti', '104254753', '7', 'Eviyuniarti', 'PPC'],
            ['Beatary Gunawan', '101233033', '7', 'Beatary', 'PPC'],
            ['Abri', '104212821', '7', 'Abri', 'PPC'],
            ['Retary Anta Oktavia Lubis', '104212824', '7', 'Retary', 'PPC'],
            ['Fatarulian Gultom', '104212788', '7', 'Fatarulian', 'PPC'],
            ['Indah Makruai', '104102456', '7', 'Indahmakruai', 'PPC'],
            ['Waridi Putra', '104102552', '7', 'Waridi', 'PPC'],
            ['Supriatman Patondiu', '104102500', '7', 'Supriatman', 'PPC'],
            ['Santiana Dedi', '104212701', '7', 'Santiana', 'PPC'],
            ['Taufiqumrahman', '103133618', '7', 'Taufiqumrahman', 'PPC'],
            ['Maksum Arief', '103223592', '7', 'Maksumarief', 'PPC'],
            ['Aminudi Syafaruddin', '101222951', '7', 'Syafaruddin', 'PPC'],
            ['Joksan Sedayanku', '101222915', '7', 'Joksansedayanku', 'PPC'],
            ['Joksan Seruji', '101322131', '7', 'Joksanseruji', 'PPC'],
            ['Anwar Arifin', '104212732', '7', 'Anwararifin', 'PPC'],
            ['Elvis Arnoldo Teixeira', '101233133', '7', 'Elvis', 'PPC'],
            ['Nur Lathifah', '101223202', '7', 'Nurlathifah', 'PPC'],
            ['Yuhun', '104122822', '7', 'Yuhun', 'PPC'],
            ['Aminto Sumasong', '104212816', '7', 'Aminto', 'PPC'],
            ['Roza Windi Sewentina', '102233632', '7', 'Roza', 'Purchasing'],
            ['Claudya Steffani W.', '102233621', '7', 'Claudya', 'Purchasing'],
            ['Nabila Nurina Ayundia', '101223407', '7', 'Nabilanurina', 'Purchasing'],
            ['Amalina', '104234033', '7', 'Amalina', 'Purchasing'],
            ['Abdul Rahman', '104233783', '7', 'Abdulrahman', 'Purchasing'],
            ['Kokoh', '104234011', '7', 'Kokoh', 'QA/QC'],
            ['Bagus Setiawan Permana', '104233618', '7', 'Bagussetiawan', 'QA/QC'],
            ['Devi Yana Kurnia', '104233624', '7', 'Devi', 'QA/QC'],
            ['Ridho Wid. Utama', '104222952', '7', 'Ridho', 'QA/QC'],
            ['Waluy Kristanto', '104222953', '7', 'Waluy', 'QA/QC'],
            ['Sri Ayu Lestari', '104202348', '7', 'Sriayulestari', 'QA/QC'],
            ['Aswan Pasandi', '104223338', '7', 'Aswanpasandi', 'QA/QC'],
            ['Ivanna Bridga Ramadhan', '104223339', '7', 'Ivanna', 'QA/QC'],
            ['Giban AqyanalquDah', '104254593', '7', 'Giban', 'QA/QC'],

            // SEKUPANG 1 (Regional 3 - Location 8)
            ['Julaman Manurung', '103123738', '8', 'Juluaman', 'Engineering'],
            ['Fengki', '104230233', '8', 'Fengki', 'HR'],
            ['Reni Fransiska Br Sianipar', '104211221', '8', 'Reni', 'HSE'],
            ['Hanuna Mustika Sanjaya Kelly', '103223703', '8', 'Hanuna', 'Production PMT'],
            ['Abdurrahman Wahab Nasu', '103222593', '8', 'Nasu', 'Project Management'],
            ['Nabila Ainun Nur Rahwat', '103233862', '8', 'Nabilaainun', 'Project Management'],
            ['Jondry Julie Asbury', '103233528', '8', 'Jondry', 'Project Management'],
            ['Michael, S.T.', '103233704', '8', 'Michael', 'Project Management'],
            ['Bijaksana Sembiring', '104234111', '8', 'Bijaksana', 'Quality Control'],
            ['Salemina Ch. Rambeany', '104233703', '8', 'Salemina', 'Quality Control'],
            ['Niko Agung Pratama. T', '104234011', '8', 'Nikoagung', 'Quality Control'],
            ['Heriadi', '103192318', '8', 'Heriadi', 'Warehouse'],
            ['Suci Atika Rahmadani', '103223243', '8', 'Suciatika', 'Warehouse'],
            ['Siti Nurfauziah', '103233752', '8', 'Sitinurfauziah', 'Warehouse'],

            // SEKUPANG 2 (Regional 3 - Location 9)
            ['Andri Usmany', '103234002', '9', 'Andri', 'Facility'],
            ['Yudo Mukti', '103233723', '9', 'Yudomukti', 'HSE'],
            ['Irsal', '103223205', '9', 'Irsal', 'Production PMT'],
            ['Ceatarius M. Lekatempery', '103233782', '9', 'Ceatarius', 'Project Management'],
            ['Yustinus Tarigan', '103233762', '9', 'Yustinus', 'Quality Control'],
            ['Ersela Pentury', '103223339', '9', 'Ersela', 'Quality Control'],
            ['Hendri', '103223014', '9', 'Hendra', 'Warehouse'],
            ['Elsa Irene Theresa', '103233705', '9', 'Elsa', 'Warehouse'],
        ];

        foreach ($users as $u) {
            $username = strtolower($u[3]);
            
            // Check if this username is already taken by a DIFFERENT NIK
            $existing = DB::table('users')->where('username', $username)->first();
            if ($existing && $existing->nik !== $u[1]) {
                $username = $username . '.' . $u[1];
            }

            DB::table('users')->updateOrInsert(
                ['nik' => $u[1]],
                [
                    'name' => $u[0],
                    'username' => $username,
                    'location_id' => (int) $u[2],
                    'region_id' => 3,
                    'password' => $password,
                    'department_id' => $departments[$u[4]],
                    'role' => 'user',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
