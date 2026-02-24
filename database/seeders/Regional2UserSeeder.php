<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use Carbon\Carbon;

class Regional2UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('tiket-ktu2');

        $departments = [
            'Shipyard' => Department::firstOrCreate(['name' => 'Shipyard'])->id,
            'Administration' => Department::firstOrCreate(['name' => 'Administration'])->id,
            'Office' => Department::firstOrCreate(['name' => 'Office'])->id,
            'Business Development' => Department::firstOrCreate(['name' => 'Business Development'])->id,
            'Sales & Marketing' => Department::firstOrCreate(['name' => 'Sales & Marketing'])->id,
            'Engineering' => Department::firstOrCreate(['name' => 'Engineering'])->id,
            'Facility' => Department::firstOrCreate(['name' => 'Facility'])->id,
            'HR' => Department::firstOrCreate(['name' => 'HR'])->id,
            'HSE' => Department::firstOrCreate(['name' => 'HSE'])->id,
            'IT' => Department::firstOrCreate(['name' => 'IT'])->id,
            'ISO' => Department::firstOrCreate(['name' => 'ISO'])->id,
            'Legal' => Department::firstOrCreate(['name' => 'Legal'])->id,
            'PPIC' => Department::firstOrCreate(['name' => 'PPIC'])->id,
            'Production' => Department::firstOrCreate(['name' => 'Production'])->id,
            'Project Management' => Department::firstOrCreate(['name' => 'Project Management'])->id,
            'Purchasing' => Department::firstOrCreate(['name' => 'Purchasing'])->id,
            'Quality Control' => Department::firstOrCreate(['name' => 'Quality Control'])->id,
            'Warehouse' => Department::firstOrCreate(['name' => 'Warehouse'])->id,
            'Production PMT' => Department::firstOrCreate(['name' => 'Production PMT'])->id,
            'Production Workshop' => Department::firstOrCreate(['name' => 'Production Workshop'])->id,
            'Security' => Department::firstOrCreate(['name' => 'Security'])->id,
            'QC' => Department::firstOrCreate(['name' => 'Quality Control'])->id,
        ];

        // Ensure missing departments exist
        foreach (['Shipyard', 'Administration', 'Facility', 'Production PMT', 'Production Workshop', 'PPIC', 'Business Development'] as $deptName) {
            if (!isset($departments[$deptName])) {
                 $departments[$deptName] = Department::firstOrCreate(['name' => $deptName])->id;
            }
        }

        $users = [
            // SAGULUNG (Regional 2 - Location 3)
            ['Setiawan', '102022017', '3', 'Setiawan', 'Shipyard'],
            ['Yenny Indriani', '102011001', '3', 'Yenny', 'Administration'],
            ['Robin Pramana', '102031004', '3', 'Robin', 'Office'],
            ['Amara Hutapea', '102223373', '3', 'Amara', 'Administration'],
            ['Syonia Veronica', '102223374', '3', 'Syonia', 'Administration'],
            ['Resti Nur Karimah', '102233860', '3', 'Resti', 'Administration'],
            ['Sarmila Aulia Sitorus', '102233861', '3', 'Sarmila', 'Administration'],
            ['Agus', '102102140', '3', 'Agus', 'Administration'],
            ['Herly Nurlinda', '102192454', '3', 'Herly.nurlinda', 'Administration'],
            ['Ibnu Maulana', '102233727', '3', 'Ibnu.maulana', 'Administration'],
            ['Ica Yolanda Ginting', '102233760', '3', 'Ica', 'Administration'],
            ['Wati', '102081009', '3', 'Wati', 'Administration'],
            ['Handayani', '102202549', '3', 'Handayani', 'Administration'],
            ['Erin Tifani Kristin', '102233465', '3', 'Erin', 'Administration'],
            ['Diana Novita Simanullang', '102223060', '3', 'Diana', 'Administration'],
            ['Rista Faradhilla Rahmadani', '102244196', '3', 'Rista', 'Administration'],
            ['Andy', '102131131', '3', 'Andy', 'Administration'],
            ['Sri Meilya Fransiska', '102244146', '3', 'Srimeilya', 'Administration'],
            ['Calvin Hausjah', '102192404', '3', 'Calvin', 'Business Development'],
            ['Ivan Harya Putranto', '102223341', '3', 'Ivan', 'Business Development'],
            ['Annisa Syahrawani', '104223228', '3', 'Annisa', 'Sales & Marketing'],
            ['Muh. Fadhel Sandy', '102223204', '3', 'Fadhel', 'Sales & Marketing'],
            ['Stephanus Maruanaja', '102021071', '3', 'Stephanus', 'Engineering'],
            ['Yogi Kuswandi', '102142255', '3', 'Yogi', 'Engineering'],
            ['Santi Yuliani', '102112014', '3', 'Santi', 'Engineering'],
            ['Wina Kurnia Fitri Salsabila', '104222880', '3', 'Wina', 'Engineering'],
            ['Muhaimin As\'Adi M. Ranahedy', '104223003', '3', 'Asadi', 'Engineering'],
            ['Alifah Ummu Zakiah', '102223344', '3', 'Alifah', 'Engineering'],
            ['Khairunnisa', '102223385', '3', 'Khairunnisa', 'Engineering'],
            ['Nindi Utami Putri', '102233634', '3', 'Nindi', 'Engineering'],
            ['Aliefah Alenasari Shilma', '102223376', '3', 'Aliefah', 'Engineering'],
            ['Juan Carlos Tambunan', '102223370', '3', 'Juan', 'Engineering'],
            ['Asta Nusa Abdul Aziz', '102233700', '3', 'Astanusa', 'Engineering'],
            ['Irfan Wahyudin', '102233721', '3', 'Irfan', 'Engineering'],
            ['Fadhila Zuria Arifin', '102233755', '3', 'Fadhila', 'Facility'],
            ['Andri Dedi Cahyanto', '102244160', '3', 'Andrie', 'Facility'],
            ['Salwan. SH', '102012021', '3', 'Salwan', 'HR'],
            ['Putri Wahyuni', '102092022', '3', 'Putriwahyuni', 'HR'],
            ['Windy Anastasya. A', '102212837', '3', 'Windy', 'HR'],
            ['Nur Hayatna Nasution', '102233408', '3', 'Nurhayatna', 'HR'],
            ['Annisa Ramadhani Chandra', '102233518', '3', 'Annisaramadhan', 'HR'],
            ['Fitri Rahayu Odelia', '102233119', '3', 'Fitri', 'HR'],
            ['Surino', '102112076', '3', 'Surino', 'HR'],
            ['Boy Marthin Butar Butar', '102223214', '3', 'Boymarthin', 'HSE'],
            ['Baso Chaeril Febrian Vatwa Perkasa', '102223390', '3', 'Basochaeril', 'HSE'],
            ['Sostri Purwito', '102192417', '3', 'Sostri', 'ISO'],
            ['Ferdinal Sukman Nur', '102233679', '3', 'Ferdinal', 'IT'],
            ['Sri Wahyuni', '102081008', '3', 'Sriwahyuni', 'Legal'],
            ['Mutiara Asri I.M', '102071007', '3', 'Mutiara', 'Legal'],
            ['Sampe Marbun', '102202643', '3', 'Sampe', 'Legal'],
            ['Clara Anastasia Febiola Marpaung', '102244125', '3', 'Clara', 'Legal'],
            ['Zul Fitriyah', '102011002', '3', 'Zulfitriyah', 'PPIC'],
            ['Yecika Bella Kristin', '102202679', '3', 'Yecika', 'PPIC'],
            ['Chairunnisah', '102212743', '3', 'Chairunnisah', 'PPIC'],
            ['Nadia Putri Julita', '102244068', '3', 'Nadia', 'PPIC'],
            ['Zery Kustiadi', '102142258', '3', 'Zery', 'Production'],
            ['Michaeleno Engelheart Lukas', '102222971', '3', 'Michaeleno', 'Production'],
            ['Amara Trada Putri Adriana', '102223234', '3', 'Amara', 'Production'],
            ['Esty Octa Marlina Br Sirait', '102233521', '3', 'Esty', 'Production'],
            ['Chintya Putri Ayu Lestari', '102233925', '3', 'Chintya', 'Production'],
            ['Inra', '102182342', '3', 'Indra', 'Project Management'],
            ['Benediktus S.T', '102062288', '3', 'Benediktus', 'Project Management'],
            ['Resita Romauli Manurung', '102102102', '3', 'Resita', 'Project Management'],
            ['Harlan Rizky Fauzi, S.T', '104233758', '3', 'Harlan', 'Project Management'],
            ['Hendri', '102121123', '3', 'Hendri', 'Project Management'],
            ['Sutrisman', '102202622', '3', 'Sutrisman', 'Project Management'],
            ['Siti Hidayati Ramdani', '102223249', '3', 'Sitihidayati', 'Project Management'],
            ['Heni Tri Wahyuni', '102101010', '3', 'Heni', 'Purchasing'],
            ['Karlina', '102102104', '3', 'Karlina', 'Purchasing'],
            ['Widya Nusram', '102223340', '3', 'Widya', 'Purchasing'],
            ['Novila Muhsana', '102233678', '3', 'Novila', 'Purchasing'],
            ['Meganaya Pertiwi', '102244177', '3', 'Meganaya', 'Purchasing'],
            ['Teguh Waluyo', '102062107', '3', 'Teguh', 'Quality Control'],
            ['Akhmad Firdaus', '102092111', '3', 'Akhmadfirdaus', 'Quality Control'],
            ['Nanang Saputro', '102112118', '3', 'Nanang', 'Quality Control'],
            ['Nidia Yulianti', '102122186', '3', 'Nidia', 'Quality Control'],
            ['Indah Purnamasari', '102192476', '3', 'Indah', 'Quality Control'],
            ['M. Arif', '102223335', '3', 'Arif', 'Quality Control'],
            ['Lamaroh Wilson Pintua', '102223337', '3', 'Lamaroh', 'Quality Control'],
            ['Aulia Hamonangan', '102233507', '3', 'Aulia', 'Quality Control'],
            ['Habil', '102233527', '3', 'Habil', 'Quality Control'],
            ['Nurgianto', '102233528', '3', 'Nurgianto', 'Quality Control'],
            ['Paulinus Frederikus Balubun', '102233698', '3', 'Paulinus', 'Quality Control'],
            ['Agus Wahyudi', '102233822', '3', 'Aguswahyudi', 'Quality Control'],
            ['Dean Baskoro Aji Pratama', '102233862', '3', 'Deanbaskoro', 'Quality Control'],
            ['Arif Budi Jatmiko', '102233863', '3', 'Arifbudi', 'Quality Control'],
            ['Mujid Kurmidianata', '102244191', '3', 'Mujid', 'Quality Control'],
            ['Asih Budhi Wahyuni', '102042097', '3', 'Asih', 'Warehouse'],
            ['Nurdin S', '102122163', '3', 'Nurdin', 'Warehouse'],
            ['Nova Kristina Hutabarat', '102192377', '3', 'Nova', 'Warehouse'],
            ['Marni Napitupulu', '102202551', '3', 'Marni', 'Warehouse'],
            ['Eva Susanti Purba', '102212738', '3', 'Eva', 'Warehouse'],
            ['Putri Syarqiya', '102244066', '3', 'Putrisyarkiya', 'Warehouse'],

            // TANJUNG UNCANG (Regional 2 - Location 4)
            ['Aji Masdar', '102092020', '4', 'Aji', 'Shipyard'],
            ['Dedi Akon', '102022033', '4', 'Dediakon', 'Facility'],
            ['Dwi Yoga Prasetya', '102202651', '4', 'Dwiyoga', 'Facility'],
            ['Suharman', '102212732', '4', 'Suharman', 'Facility'],
            ['Amran', '102202594', '4', 'Amran', 'Facility'],
            ['Munsaid', '102202649', '4', 'Munsaid', 'Facility'],
            ['Akhmad Nurul Madin', '102052025', '4', 'Nurulmadin', 'Facility'],
            ['Agus Supriyadi', '102112055', '4', 'Agussupriyadi', 'Facility'],
            ['Enggi Prasetya', '102152307', '4', 'Enggi', 'Facility'],
            ['Rahmad Ariadi', '102112011', '4', 'Rahmadariadi', 'Facility'],
            ['Deddy Kohar', '102042026', '4', 'Deddykohar', 'Facility'],
            ['Sugiyanto', '102202650', '4', 'Sugiyanto', 'Facility'],
            ['Irako Atma Negara', '102112009', '4', 'Irako', 'Facility'],
            ['Muhammad Ridho', '102202630', '4', 'Muhammadridho', 'Facility'],
            ['Nugroho Lesmono', '102052106', '4', 'Nugroho', 'Facility'],
            ['Maryanto', '102032036', '4', 'Maryanto', 'Facility'],
            ['Solihan', '102202622', '4', 'Solihan', 'Facility'],
            ['Sugiono', '102254786', '4', 'Sugiono', 'Facility'],
            ['Adi Warda', '104233646', '4', 'Adiwarda', 'Facility'],
            ['Dede Saputra', '102223371', '4', 'Dedesaputra', 'Facility'],
            ['Zubairi Misbah', '102142258', '4', 'Zubairi', 'Facility'],
            ['Putu Setiawan', '102082298', '4', 'Putu', 'Facility'],
            ['Yudi Sutisna', '102112283', '4', 'Yudisutisna', 'Facility'],
            ['Abdul Kohar', '102131016', '4', 'Abdulkohar', 'Facility'],
            ['Muhammad Nasir', '102112151', '4', 'Muhammadnasir', 'Facility'],
            ['Ikhwan Azis P', '102233699', '4', 'Ikhwanazis', 'Facility'],
            ['Maulana Ichsan Syahdana', '102254612', '4', 'Maulanaichsan', 'Facility'],
            ['Wahyudi Al Faiq', '102212805', '4', 'Wahyudi', 'Facility'],
            ['Ahmad Kori', '102051015', '4', 'Ahmadkori', 'HR'],
            ['Supartono', '102112085', '4', 'Supartono', 'HR'],
            ['Sayadi', '102022033', '4', 'Sayadi', 'HR'],
            ['Endro Wahyudin', '102202616', '4', 'Endrowahyudin', 'HR'],
            ['Munasar P. Maruheny', '102021071', '4', 'Munasar', 'HR'],
            ['Ramdani', '102132214', '4', 'Ramdani', 'HR'],
            ['Sukurino Laba', '102102073', '4', 'Sukurino', 'HR'],
            ['Mulud Bin Suganda', '102112084', '4', 'Mulud', 'HR'],
            ['Muhammad Abdul Hamid', '102052023', '4', 'Abdulhamid', 'HR'],
            ['Muhammad Solehan', '102212730', '4', 'Solehan', 'HR'],
            ['Imron Sakik', '102202594', '4', 'Imronsakik', 'HR'],
            ['Novika Purtiani', '102233634', '4', 'Novika', 'HR'],
            ['Aminudi', '102212811', '4', 'Aminudi', 'Production PMT'],
            ['Aris Khaerul', '102062300', '4', 'Ariskhaerul', 'Production Workshop'],
            ['Bambang Supriyadi', '102102008', '4', 'Bambang', 'Production Workshop'],
            ['Dedi Surya Wijaya', '102092131', '4', 'Dedisurya', 'Production Workshop'],
            ['Dadi Alis Syahputra', '102202654', '4', 'Dadialis', 'Production Workshop'],
            ['Nurhayati', '102222915', '4', 'Nurhayati', 'Production Workshop'],
            ['Sugeng Heru', '102132215', '4', 'Sugengheru', 'Production Workshop'],
            ['Aris Guntoro', '102122296', '4', 'Arisguntoro', 'Project Management'],
            ['Asri', '102132226', '4', 'Asri', 'Project Management'],
            ['Andika Saputra', '102132227', '4', 'Andikasaputra', 'Quality Control'],
            ['Muh. Sandy Wicaksono', '102223241', '4', 'Sandywicaksono', 'Quality Control'],
            ['Rahmat Firdaus', '102112118', '4', 'Rahmatfirdaus', 'Quality Control'],
            ['Yusup Kurniawan', '102102127', '4', 'Yusup', 'Quality Control'],
            ['Arif Hidayat', '102092123', '4', 'Arifhidayat', 'Quality Control'],
            ['Darmansyah Agus', '102022120', '4', 'Darmansyah', 'Quality Control'],
            ['Agus Abadi Putra', '102112295', '4', 'Agusabadi', 'Quality Control'],
            ['Rosihan Anwarudi', '102092020', '4', 'Rosihan', 'Quality Control'],
            ['Daryuddin', '102021071', '4', 'Daryuddin', 'Quality Control'],
            ['Adi Pariyadi', '102082001', '4', 'Adipariyadi', 'Warehouse'],
            ['Sugeng Mulyanto', '102062275', '4', 'Sugeng', 'Warehouse'],
            ['Agus Setiawan Saputra', '102132236', '4', 'Agussetiawan', 'Warehouse'],
            ['Resa Brutus Paiman', '102132223', '4', 'Resabrutus', 'Warehouse'],
            ['Rowiatul Khanisah. A', '102244425', '4', 'Rowiatul', 'Warehouse'],
            ['Topo Kurniawan', '102062301', '4', 'Topokurniawan', 'Warehouse'],
            ['Jenal Agusrondi', '102112076', '4', 'Jenal', 'Warehouse'],
            ['Ali Mukti', '102112302', '4', 'Alimukti', 'Warehouse'],
            ['Leka Astura', '102112303', '4', 'Leka', 'Warehouse'],
            ['Lubis', '102132231', '4', 'Lubis', 'Warehouse'],
            ['Muhtarom Waluyo', '102122176', '4', 'Muhtarom', 'Warehouse'],
            ['Idris', '102112083', '4', 'Idris', 'Warehouse'],
            ['Reda Turino', '102102056', '4', 'Redaturino', 'Warehouse'],
            ['Sarah Maha Mursyidah', '102223103', '4', 'Sarahmaha', 'Warehouse'],
            ['Samani', '102244365', '4', 'Samani', 'Project Management'],
            ['Alfan Wahyu Prayoga', '102212732', '4', 'Alfan', 'Quality Control'],
            ['Galah Arsyad Mustafa', '102223335', '4', 'Galah', 'Quality Control'],
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
                    'region_id' => 2,
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
