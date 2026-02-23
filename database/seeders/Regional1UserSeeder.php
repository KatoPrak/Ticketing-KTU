<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class Regional1UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('tiket-ktu1');

        $departments = [
            'HR' => 3,
            'FAT' => 8, // Accounting
            'ICMS' => Department::firstOrCreate(['name' => 'ICMS'])->id,
            'IT' => 9,
            'Sales & Marketing' => 22,
            'Legal' => 13,
            'Purchasing' => 6,
            'Treasury' => Department::firstOrCreate(['name' => 'Treasury'])->id,
            'BOD' => Department::firstOrCreate(['name' => 'BOD'])->id,
            'Head Office Eksternal' => Department::firstOrCreate(['name' => 'Head Office Eksternal'])->id,
            'RND' => Department::firstOrCreate(['name' => 'RND'])->id,
            'Operation Support' => Department::firstOrCreate(['name' => 'Operation Support'])->id,
            'QC' => 19,
            'Project Management' => 15,
            'Admin Shipyard' => Department::firstOrCreate(['name' => 'Admin Shipyard'])->id,
            'Warehouse' => 16,
            'PPIC' => 11,
            'Mechanical Facility' => Department::firstOrCreate(['name' => 'Mechanical Facility'])->id,
        ];

        $users = [
            // JAKARTA (Regional 1 - Location 1)
            ['Abdul Hafidz', '101190044', '1', 'Abdul', 'HR'],
            ['Angga Maulana', '101170001', '1', 'Angga', 'HR'],
            ['Astrid Puspitasari', '101210002', '1', 'Astrid', 'FAT'],
            ['Bayu Hengki Buluwaei', '101230089', '1', 'Bayu', 'ICMS'],
            ['Bellatrix Harry Grace', '101230100', '1', 'Bellatrix', 'FAT'],
            ['Budi Hartono', '101230079', '1', 'Budi', 'IT'],
            ['Cetrun Lesmana', '101200003', '1', 'Cetrun', 'Sales & Marketing'],
            ['Daniel Setiawan', '101220083', '1', 'Daniel', 'Legal'],
            ['Decky Prayoga', '101120010', '1', 'Decky', 'HR'],
            ['Dendy Yunusa', '101210011', '1', 'Dendy', 'Sales & Marketing'],
            ['Djuita Darwin', '101010009', '1', 'Djuita', 'Purchasing'],
            ['Edi Andika', '1011240117', '1', 'Edi_andika', 'HR'],
            ['Edi Setiawan', '101190043', '1', 'Edi_setiawan', 'HR'],
            ['Eko Kuswoyo', '101220075', '1', 'Eko', 'HR'],
            ['Esther Ratuning Pawestry', '101240106', '1', 'Esther', 'IT'],
            ['Eva Arisvita', '101190012', '1', 'Eva.arsvita', 'Treasury'],
            ['Evangelina Valessia Kwandy', '101230129', '1', 'Evangelina', 'Treasury'],
            ['Ewan Iwan Saragih', '101230122', '1', 'Ewan', 'ICMS'],
            ['Farra Rachman', '101120013', '1', 'Farra', 'Treasury'],
            ['Febi Anastasia', '101240104', '1', 'Febi', 'HR'],
            ['Fitri Odelia', '101230119', '1', 'Fitri', 'Purchasing'],
            ['Gideon Tjahja Purnomo', '101190014', '1', 'Gideon', 'FAT'],
            ['Gilbert', '101230123', '1', 'Gilbert', 'FAT'],
            ['Githa Margaret', '101230120', '1', 'Githa', 'Purchasing'],
            ['Harno', '101170042', '1', 'Harno', 'HR'],
            ['Henry Chandra', '101220077', '1', 'Henry', 'FAT'],
            ['Husni Arief', '101130016', '1', 'Husni', 'Sales & Marketing'],
            ['Imelda Estirita', '101220087', '1', 'Imelda', 'FAT'],
            ['Jaka Syahputra', '101230096', '1', 'Jaka', 'FAT'],
            ['John Winata', '101240110', '1', 'John', 'IT'],
            ['Keith Aditya Urijan', '101210018', '1', 'Keith', 'FAT'],
            ['Ketty', '101230091', '1', 'Ketty', 'FAT'],
            ['Kevin Murtano', '101220073', '1', 'Kevin', 'FAT'],
            ['King Effendy', '101220086', '1', 'King', 'HR'],
            ['Linda', '101000019', '1', 'Linda', 'Treasury'],
            ['Livia Wikani', '101230097', '1', 'Livia', 'Sales & Marketing'],
            ['Malik Atikun', '101240116', '1', 'Malik', 'HR'],
            ['Maria Magdalena Laksmi Arete', '101210021', '1', 'Maria', 'HR'],
            ['Mattew Nicky A. N', '101240108', '1', 'Mattew', 'Legal'],
            ['Mei Mufyani', '101230001', '1', 'Mei', 'HR'],
            ['Meli Iriyanty', '101240103', '1', 'Meli', 'Treasury'],
            ['Melody Tjihin', '101160020', '1', 'Melody', 'Sales & Marketing'],
            ['Mustafa', '101199039', '1', 'Mustafa', 'HR'],
            ['Nathania Frederica', '101230124', '1', 'Nathania', 'FAT'],
            ['Putri', '1011220062', '1', 'Putri', 'HR'],
            ['Raden Sultan Febryandra', '101220067', '1', 'Sultan', 'HR'],
            ['Ratnawati', '101090025', '1', 'Ratnawati', 'Treasury'],
            ['Regina Celine Intansari', '101230118', '1', 'Regina', 'FAT'],
            ['Ricky Sanjaya', '101060026', '1', 'Ricky', 'Sales & Marketing'],
            ['Riza Faqih', '101230121', '1', 'Riza', 'Sales & Marketing'],
            ['Sadimin', '101120040', '1', 'Sadimin', 'HR'],
            ['Saepudin', '101130041', '1', 'Saepudin', 'HR'],
            ['Shela Apriani', '101240111', '1', 'Shela', 'FAT'],
            ['Suhami Subani', '101030028', '1', 'Suhami', 'Treasury'],
            ['Surya Tjandra', '101230102', '1', 'Surya', 'IT'],
            ['Susilowati', '101030030', '1', 'Susilowati', 'Treasury'],
            ['Tini Supartini', '101070032', '1', 'Tini', 'HR'],
            ['Titin Nurhayati', '101070033', '1', 'Titin', 'HR'],
            ['Vindy Malakombo', '101230128', '1', 'Vindy', 'BOD'],
            ['Wahyuda S', '101220076', '1', 'Wahyuda', 'HR'],
            ['Wardono Asnim', '101220055', '1', 'Wardono', 'BOD'],
            ['Wisly Prayogi S.', '101220084', '1', 'Wisly', 'FAT'],
            ['Wiliany', '101110033', '1', 'Wiliany', 'Sales & Marketing'],
            ['Winarto Asnim', '101220057', '1', 'Winarto', 'BOD'],
            ['Yohana R. J. Sihombing', '101230101', '1', 'Yohana', 'Purchasing'],
            ['Yuliana Ivanna Irwadin', '101230099', '1', 'Yanni', 'HR'],
            ['Yusra Titin Mariana', '101240105', '1', 'Yusra', 'Treasury'],
            ['David Sakti Satyawan', '102213818', '1', 'David', 'Head Office Eksternal'],
            ['Dharma', '101230093', '1', 'Dharma', 'Head Office Eksternal'],
            ['Meithana', '101230132', '1', 'Meithana', 'Head Office Eksternal'],
            ['Peter Setiawan', '101230094', '1', 'Peter', 'Head Office Eksternal'],
            ['Stephanus Widi Hardono', '101230123', '1', 'Widi', 'RND'],
            ['Rangga Al Khadry', '101230126', '1', 'Rangga', 'RND'],
            ['Elsadai Seras Simbolon', '101230127', '1', 'Elsadai', 'Operation Support'],
            ['Diniar Anggraini Nurusman', '101230130', '1', 'Diniar', 'RND'],
            ['Andika Maulana Ibrahim', '101230131', '1', 'Andika', 'RND'],
            ['Arif Hidayat', '101230133', '1', 'Arif Hidayat', 'RND'],

            // MARUNDA (Regional 1 - Location 2)
            ['Ajai Sembodo', '105220020', '2', 'Ajai', 'QC'],
            ['Aliansyah', '105230005', '2', 'Aliansyah', 'Warehouse'],
            ['Dimas Agung Purnomo', '105220019', '2', 'Dimas', 'Project Management'],
            ['Fachry Ramadhan', '105220016', '2', 'Fachry', 'QC'],
            ['Fazri Yanti Nur Safanah', '105230004', '2', 'Fazri', 'Admin Shipyard'],
            ['Ferryanto Cahyadi', '105230007', '2', 'Ferryanto', 'Admin Shipyard'],
            ['Lukianggara', '105240002', '2', 'Lukianggara', 'QC'],
            ['Muhamad Maaludin', '105180004', '2', 'Maaludin', 'Warehouse'],
            ['Muhammad Amri Yahya', '105220018', '2', 'Amri', 'PPIC'],
            ['Purwandi', '105030001', '2', 'Purwandi', 'Admin Shipyard'],
            ['Rika Aisyah', '105220005', '2', 'Rika', 'PPIC'],
            ['Seftiana Sugita', '105240003', '2', 'Seftiana', 'PPIC'],
            ['Waridi', '105110006', '2', 'Waridi', 'Mechanical Facility'],
            ['Y. Christiyanto', '105130003', '2', 'Christiyanto', 'Warehouse'],
        ];

        $userData = [];
        foreach ($users as $u) {
            $userData[] = [
                'name' => $u[0],
                'nik' => $u[1],
                'location_id' => (int) $u[2],
                'region_id' => 1,
                'username' => strtolower($u[3]),
                'password' => $password,
                'department_id' => $departments[$u[4]],
                'role' => 'user',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($userData, 50) as $chunk) {
            DB::table('users')->upsert($chunk, ['username'], ['name', 'nik', 'location_id', 'department_id', 'password', 'updated_at']);
        }
    }
}
