<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KTUUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $departmentMap = [
            'HSE' => 1,
            'Security' => 2,
            'HR' => 3,
            'Finance' => 4,
            'Production' => 5,
            'Production Workshop' => 5,
            'Purchasing' => 6,
            'Engineering' => 7,
            'Accounting' => 8,
            'IT' => 9,
            'Information Technology' => 9,
            'Administration' => 10,
            'PPIC' => 11,
            'Office' => 12,
            'Legal' => 13,
            'Facility' => 14,
            'Project Management' => 15,
            'Warehouse' => 16,
            'Shipyard' => 17,
            'Production PMT' => 18,
            'Quality Control' => 19,
            'Business Development' => 20,
            'ISO' => 21,
            'Sales & Marketing' => 22,
        ];
        
        // Sort keys by length descending to match longest department names first
        // This prevents 'Production' matching part of 'Production Workshop'
        uksort($departmentMap, function($a, $b) {
            return strlen($b) - strlen($a);
        });

        // 🔥 Tempel RAW DATA kamu di sini
        $rawData = <<<DATA
454	Yenny Indriani	Administration	102011001
455	Zul Fitriyah	PPIC	102011002
456	Robin Pramana	Office	102031004
457	Mutiara Asri I.M	Legal	102071007
458	Sri Wahyuni	Legal	102081008
459	Wati	Administration	102081009
460	Heni Tri Wahyuni	Purchasing	102101010
461	Supriyadi	HR	102051015
462	Agus Suyoko	HR	102131016
463	Karimul Siregar	Facility	102051024
464	Fransiskus Ewaldus	Production Workshop	102091030
465	Stephanus Maruanaja	Engineering	102021071
466	Kusnandar	Production Workshop	102121113
467	Hendri	Project Management	102121123
468	Triyono	HR	102121125
469	Andy	Administration	102131131
470	Dedy Feriyanto	HR	102131145
471	Joni Ismail	Warehouse	102082001
472	Hendrik Susanto	Facility	102032002
473	Khairul Lubis	Warehouse	102132004
474	Zainal Abidin	Production Workshop	102102008
475	II	Facility	102112009
476	Rahmad	Facility	102112011
477	Santi Yuliani	Engineering	102112014
478	Aswan / Aliang	Shipyard	102022017
479	Setiawan	Shipyard	102082019
480	Dedi Susetijo	Production PMT	102092020
481	Salwan. SH	HR	102012021
482	Putri Wahyuni	HR	102092022
483	Halim Kurnia	Shipyard	102052023
484	Parlindungan Silalahi	Facility	102012024
485	M. Nurul Amin	Facility	102052025
486	Deddy Mulyadi	Facility	102042026
487	Bambang Eko yudianto	Facility	102092027
488	Agus Setiawan	Facility	102102029
489	Adi Darmansyah	Facility	102022033
490	Maryanto	Facility	102032036
491	Irwan Dalimunthe	Facility	102052040
492	Suyanto	Facility	102072041
493	Sapran Bin Karyadi	Facility	102082047
494	M. Nurul Iman	Facility	102072048
495	Nuramin	Facility	102102051
496	Joannes Berchmans Ns	Facility	102102052
497	Wagino	Facility	102112055
498	Redi Dharma	Facility	102102056
499	Sulaiman Ndese	Facility	102112057
500	Feldinato Dos Santos	HR	102012058
501	Edyson	HR	102032060
502	Ermantho Simbolon	HR	102042061
503	Abdul Rasyid	HR	102042062
504	Yohanes Laba Dolun	HR	102052064
505	Tahi Parsaoran pakpahan	HR	102062066
506	Mulud	HR	102102073
507	Hariandi	HR	102102075
508	Surino	HR	102112076
509	Suhaemin	HR	102112079
510	Suryanto	HR	102112080
511	Idris Ishak	HR	102112083
512	Supriyadi	HR	102112084
513	Tarsono	HR	102112085
514	Hadmaneldi	HSE	102112087
515	Suroyo	HSE	102022088
516	Dahlan	HSE	102092091
517	Defriwal	HSE	102102093
518	Asih Budhi Wahyuni	Warehouse	102042097
519	Ade Sukendra	Warehouse	102072100
520	Resita Romauli Manurung	Project Management	102102102
521	Karlina	Purchasing	102102104
522	Nugroho Darmaji	Project Management	102052106
523	Teguh Waluyo	Quality Control	102062107
524	Akhmad Firdaus	Quality Control	102092111
525	Nanang Saputro	Quality Control	102112118
526	Johanis Lapu	Production Workshop	102022120
527	Nur Muhammad Sakti A	Facility	102092123
528	Fevi Andika	Production Workshop	102102127
529	Arik Wahyudi	Production Workshop	102102128
530	Tasdik Hidayat	Facility	102092131
531	Supramana	Production Workshop	102092133
532	Heru Iswanto	Production Workshop	102102136
533	Muhamad Akbar	Production Workshop	102102138
534	Agung Prehadi Wibowo	Production Workshop	102102140
535	Achmad Muchtadi R	Production Workshop	102102141
536	Yopie Santiago	Production Workshop	102102142
537	Muklizar	Production Workshop	102112144
538	Atuan	Production Workshop	102112145
539	Ndaru Preh Adi Laksono	Production PMT	102112151
540	Nurdin S	Warehouse	102122163
541	Muhtarom	Warehouse	102122176
542	Nidia Yulianti	Quality Control	102122186
543	Munjirin	Production PMT	102122204
544	Purwono	Facility	102132212
545	Andar Saleh	HR	102132214
546	Pujiono	Production Workshop	102132215
547	Amro	HSE	102132223
548	Posma Arianto	HSE	102132224
549	Ari Valentino	Production Workshop	102132226
550	Victor Ardiyanto	Facility	102132227
551	Nurfatoni	HSE	102132231
552	Indra Saputra Silitonga	HSE	102132236
553	Silvester Sape	HR	102122237
554	Yogi Kuswandi	Engineering	102142255
555	Zery Kustiadi	Production	102142258
556	Suyatman Tirtayani	Production Workshop	102102263
557	Marnityohadi	HR	102072267
558	Muallim Dalimunte	Facility	102012270
559	Sugeng Mulyanto	Warehouse	102062275
560	Herman	Facility	102052276
561	Adriansyah	Facility	102082277
562	Robi Sugara	HR	102102280
563	Safik	Facility	102062281
564	Jenal Abidin	Facility 	102112283
565	Benediktus S.T	Project Management	102062288
566	Imron Despianto	Facility	102152290
567	Jurpan Amin Damanik	Production Workshop	102102294
568	Anton	Production Workshop	102112295
569	Edison Bail	Production Workshop	102122296
570	Adi Supriyanto	Production Workshop	102082298
571	Topo Hartopo	Production Workshop	102062300
572	Martua Manalu	Production Workshop	102072301
573	Ali Usman	Production Workshop	102112302
574	Aris Imam Irsadi	Production Workshop	102102303
575	Suhariyadi S.	Production Workshop	102072304
576	Rudi	Facility	102152307
577	Haswardi Hasan	Facility 	102132308
578	Andri	Facility	102072309
579	Indra	Project Management	102182342
580	Arifin	Facility	102182343
581	Wawan Hariyanto	Facility	102182348
582	Nova Kristina Hutabarat	Warehouse	102192377
583	Calvin Hausjah	Business Development	102192404
584	Sostri Purwito	ISO	102192417
585	Deddy Putra	Production PMT	102192449
586	Herly Nurlinda	Administration	102192454
587	Muhammad Ainun Najib	Facility 	102202543
588	Handayani	Administration	102202549
589	Marni Napitupulu	Warehouse	102202551
590	Teddy Hali Prasetio	Production Workshop	102202571
591	Aman Affroni	Facility 	102202594
592	Dedi Hermawan	Facility	102202616
593	Sutrisman	Project Management	102202622
594	Roi Jesaya Hutagalung	Production PMT	102202630
595	Sampe Marbun	Legal	102202643
596	Munsaid	Facility	102202649
597	Sugiyanto	Production Workshop	102202650
598	Periyadi Cipta	Facility	102202651
599	Achmad Jahirsyah	Production Workshop	102202654
600	Sulis Priyanto	Production PMT	102202659
601	Khayun	Facility	102202673
602	Yecika Bella Kristin	PPIC	102202679
603	Ahmad Maulana	Production Workshop	102202692
604	Muhammad Johan Wahyudi	Facility	102202693
605	Slamet Agus Suprapto	Warehouse	102202695
606	Siswanto	Facility	102212730
607	Dwi Suprojo	Production PMT	102212732
608	Eva Susanti Purba	Warehouse	102212738
609	Chairunnisah	PPIC	102212743
610	Solahuddin	Facility	102212805
611	Rahmat Fredy Nasution	Production Workshop	102212811
612	Muchsin	Facility	104212832
613	Hendri Wianto	Facility	102212834
614	Windy Anastasya. A	HR	102212837
615	Josua Marganda Siregar	Facility	102222872
616	Wina Kurnia Fitri Salsabila	Engineering	104222880
617	Suriya Mudalso	Production Workshop	102222915
618	Rola Maulana Marwan	Production Workshop	102222916
619	Michaeleno Engelheart Lukas	Production	102222971
620	Muhaimin As'Adi M. Ranahedy	Engineering	104223003
621	Wawan	Production Workshop	102223045
622	Diana Novita Simanullang	Administration	102223060
623	Suryana	Warehouse	102223103
624	Edi Hartono	HR	102223147
625	Afidz Wizazul Lubis	HR	102223161
626	Muh. Fadhel Sandy	Engineering	102223204
627	Sugeng Hidayat	Production Workshop	102223205
628	Boy Marthin Butar Butar	HSE	102223214
629	Mistaruna	Facility	102223215
630	Jhoni Fachruroji	HR	102223222
631	Neliwaty Neko	HR	102223223
632	Annisa Syahrawani	Sales & Marketing	104223228
633	Amara Trada Putri Adriana	Production	102223234
634	Eky Prayoga Haminata	Production Workshop	102223241
635	Puji Winarno	Production Workshop	102223244
636	Siti Hidayati Ramdani	Project Management	102223249
637	Ponijan	Production PMT	102223275
638	Suparyo	Production Workshop	102223321
639	Siswantoyo 	Facility	102223322
640	M. Arif	Quality Control	102223335
641	Widya Nusram	Purchasing	102223340
642	Ivan Harya Putranto	Business Development	102223341
643	Ahmad Abdul Rahman	HR	102223342
644	Mus'ab	Facility	102223344
645	Alifah Ummu Zakiah	Engineering	102223345
646	Ady Priyanto	HSE	102223361
647	Wijaya Mulya	HSE	102223362
648	Roito Pratama Lubis	Facility	102223366
649	Nur Ali Usman	Facility	102223368
650	Marcelio Wilson Hutapea	Production Workshop	102223370
651	Dedy Saputra	Production Workshop	102223371
652	Odipus Aldio	Production Workshop	102223372
653	Arista Hutapea	Administration	102223373
654	Syonia Voronica	Administration	102223374
655	Aliefah Alenasari Shilma	Engineering	102223376
656	Ahmad Zainudin	HR	102223377
657	Mustaqim	HR	102223378
658	Haswindu	HR	102223379
659	Khairunnisa	Engineering	102223385
660	Baso Chaeril Febrian Vatwa Perkasa	HSE	102223390
661	Iwan Simbolon	Facility	102223394
662	Niko Siperga	Warehouse	102223407
663	Nur Hayatna Nasution	HR	102233408
664	Yusuf Ardiansyah	Warehouse	102233409
665	Viki Rahmadani	Production PMT	102233410
666	Hero Valentine	Production PMT	102233411
667	Sahat Parulian Damanik	Production Workshop	102233416
668	Nurul Huda	Facility	102233435
669	Jevrianus Ximenes	HR	102233454
670	Ridho Chahyadi	HR	102233455
671	Lasroha Harianja	HR	102233456
672	Nurkholis	Facility	102233462
673	Erin Tifani Kristin	Administration	102233465
674	Muhammad Ilham Syahputra	Production Workshop	102233466
675	Muhammad Ismail	Facility	102233488
676	Daniel Eden Nicolas Sitorus	Facility	102233502
677	Sahri	Facility	102233503
678	Aulia Hamonangan	Quality Control	102233507
679	Maulana Syidik	Production PMT	102233508
680	Annisa Ramadhani Chandra	HR	102233518
681	Indy. S	Facility	102233519
682	Ahmaddin Nasution	Facility	102233520
683	Esty Octa Marlina Br Sirait	Production	102233521
684	Habil	Quality Control	102233527
685	Nurgianto	Quality Control	102233528
686	Randa Putra Utama	Production Workshop	102233536
687	Wira Sanusi	Facility	102233538
688	Suprianto	Facility	102233539
689	Muhammad Rudi Alamsyah Putra	Sales & Marketing	104233586
690	Indra Bonapas. S	Facility	102233623
691	Nindi Utami Putri	Engineering	102233634
692	Agung Triono	Production PMT	102233635
693	Riduan	Facility	104233646
694	Panji Imam Adyanata	Production PMT	102233655
695	Wahyu Hidayatul Husdi	Facility	102233671
696	Novila Muhsana	Purchasing	102233678
697	Ferdinal Sukman Nur	IT	102233679
698	Paulinus Frederikus Balubun	Quality Control	102233698
699	Junaedy	Facility	102233699
700	Asta Nusa Abdul Aziz	Engineering	102233700
701	Sri Wahyudi	Facility	102233720
702	Irfan Wahyudin	Engineering	102233721
703	Ahsan Sabila	Production Workshop	102233724
704	Muhammad Khaeril	Facility	102233726
705	Ibnu Maulana	Administration	102233727
706	Fadhila Zuria Arifin	Facility	102233755
707	Ismangel	Production Workshop	102233756
708	Harlan Rizky Fauzi, S.T	Project Management	104233758
709	Ica Yolanda Ginting	Administration	102233760
710	Rudi Hartono	Facility 	102233765
711	Hazuar	HR	102233781
712	Randi Handika	Production PMT	102233793
713	Belly Kurniawan	Facility	102233801
714	Lukman Rafi Kurniawan	Production Workshop	102233802
715	Agus Wahyudi	Quality Control	102233822
716	Ak Yang	Facility	102233825
717	Resti Nur Karimah	Administration	102233860
718	Sarmila Aulia Sitorus	Administration	102233861
719	Dean Baskoro Aji Pratama	Quality Control	102233862
720	Arif Budi Jatmiko	Quality Control	102233863
721	Chintya Putri Ayu Lestari	Production	102233925
722	Ervan Sugiardi	HR	102233977
723	Febri Syawaldi	Production PMT	102233978
724	Ali Nurdin	Production Workshop	102234001
725	Rein Irsal Pabudu	Production PMT	102234010
726	M. Andi Handrian	Quality Control	106234015
727	Rhaka Wibowo Mukti	HR	102234032
728	Hamdani	Production Workshop	102234033
729	Herman Saputra	Production Workshop	102244040
730	Joko Nurianto	Production Workshop	102244052
731	Yogi Hidayat	Production Workshop	102244053
732	Yogi Pratama Lubis	Production PMT	102244054
733	Putri Syarqiya	Warehouse	102244066
734	Murni Wulandari	Administration	102244067
735	Nadia Putri Julita	PPIC	102244068
736	Hairil Imran	HR	102244069
737	Nuryanto	HR	102244070
738	Indra Biantoro	Facility	102244100
739	Aditya Fajarayenta	Production PMT	102244108
740	Clara Anastasia Febiola Marpaung	Legal	102244125
741	Arman	Production Workshop	104244132
742	Sri Meilya Fransiska	Administration	102244146
743	Andre Budi Cahyanto	Facility	102244160
744	Meganaya Pertiwi	Purchasing	102244177
745	Maulana Agus Kurniawan	Production PMT	102244178
746	Gustia Ari Pratama	Warehouse	102244179
747	Mujid Kurmidianata	Quality Control	102244191
748	Rista Faradhilla Rahmadani	Administration	102244196
749	Elin Suharni Gultom	Engineering	102244198
750	Sokhiman	Facility 	102244210
751	Suyono	HR	102244211
752	Ali Bustomi	HR	102244212
753	Henri	HR	102244213
754	Frisko Yulian Maheswara	Project Management	104244221
755	Nur Rachmi	Project Management	104244228
756	Ady Tio Setiawan	Production PMT	102244238
757	Ilham Ramadhan	Business Development	102244336
758	Lilik Purwanto	Facility	102244348
759	Komariyadi	Facility	102244349
760	Mangubat Malau	Production Workshop	102244350
761	Yunus	Production Workshop	102244353
762	Endang Suprianto	Facility	102244358
763	Aprianto	Warehouse	102244365
764	Anjar Prambudhi	Facility	102244366
765	Moh. Al Fadlil Wafi	Engineering	102244370
766	Anwar	Production Workshop	102244371
767	Safaruddin	Production Workshop	102244372
768	Din Hamzah	Quality Control	102244393
769	Alex Sander 	Warehouse	102244400
770	Eko Saputra	Production Workshop	102244406
771	Asep Syamsudin	Production Workshop	102244408
772	Bagus Panji Saputra	Production Workshop	102244411
773	Amri	Warehouse	102244425
774	Romendra	Production Workshop	102244427
775	Muhammad Rizky	Production Workshop	102244458
776	Musli Hakiki Nasution	Production Workshop	102244459
777	Robed Simanjutntak	Production PMT	102244468
778	Santonius	Production Workshop	102244469
779	Hanafi Anjasmara	Facility	102244485
780	Arib Wibowo	Facility	102244486
781	Dani Suganda	Production Workshop	102244487
782	Ahmad Musa	Production Workshop	102244488
783	Alfi Fajariawan	Quality Control	102244512
784	Muhammad Paisal	Sales & Marketing	104244520
785	Jefri	Production Workshop	102244525
786	Moria Ester Adelina Br. Butar - Butar	Administration	102244526
787	Topan Chairil Mukminin	HR	102244527
788	Intan Parashinta	HR	102244536
789	Alivakhoirullisa	Legal	102244537
790	Delta Liona Aritonang	Quality Control	102244538
791	Ahmad Nasrullah	HR	102244541
792	Cici Jumifha Br Ginting	Engineering	102244542
793	Ade Rahmat Hidayat	Production Workshop	102244545
794	Baharuddin	Production Workshop	102244547
795	Nasip Parsaulian Simare - Mare	Production PMT	102244548
796	Pramono	HSE	102244551
797	Hamzah Siddik	Production Workshop	102244552
798	Arif Jupriadi	Facility	102244553
799	Iko Putra	Production Workshop	102244555
800	Amrianto	Facility	102254557
801	Satriyo Utomo	Production Workshop	102254558
802	Mikhael Boby Aktau Sianipar	Production Workshop	102254559
803	Oktoharis Hidayat	Production PMT	102254562
804	Muh. Syah Zidan	Production PMT	102254564
805	Ismayadi	Quality Control	102254565
806	Yoel Marthen Te'dang	Quality Control	102254566
807	Bagus Dwi Pangestu	Production PMT	102254568
808	Muhammad Zulfikar Brigade Putra	Quality Control	102254569
809	Taufan Adriansyah Ramadhan	Production PMT	102254571
810	Dwi Tya Yunita Sari	Production Workshop	102254574
811	Afrikil	HR	102254575
812	Mukhlasin	HR	102254599
813	Muhammad Puja	Production Workshop	102254608
814	Mahesa Wirga	Production Workshop	102254609
815	Muhammad Rino Bawana Puspito	Production Workshop	102254611
816	Mohammad Ishom Rifdath Maulana	Project Management	102254612
817	Rahmat Kurnia Mirta	Production PMT	102254613
818	Rohmat Yudi	Production Workshop	102254614
819	Yani Risa Suharto	Production Workshop	102254630
820	Rey Harris Andreas	Production Workshop	102254637
821	Khairol Adrian	Production Workshop	102254702
822	Adi Yarman	Production Workshop	102254709
823	Aslin	Production Workshop	102254714
824	Rahmatul Fajar	Production Workshop	102254736
825	Muhammad Habib	Facility	102254753
826	Humaidi	Production Workshop	102254754
827	Afriandi	Production Workshop	102254755
828	Yusdianto	Production Workshop	102254757
829	M. Yusup Parningotan Hrp	Production PMT	102254766
830	Muhammad Endy Rifa'i	Project Management	102254769
831	Ervin Rafindo Manullang	Facility	102254770
832	Dedi Wahyudi	Production Workshop	102254771
833	Muhammad Fajar	Production Workshop	102254772
834	Oky Abdul Aziz	Production Workshop	102254773
835	Busrianto	Facility	102254774
836	Purwanto	HSE	102254782
837	Tubagus Arya Nugraha	Engineering	102254783
838	Sarono	Facility	102254786
839	Sigit Bangun Umboro	Production Workshop	102254788
840	Ahmad Riadi	HR	102254789
841	Muhammad Yossi Ridho Supardi	Facility	102254791
842	Denis	Production Workshop	102254792
843	Lutfi Ramadhani Fariski	Production Workshop	102254836
844	Idris Andrian	Production PMT	102254838
845	Koko Santoso	Production Workshop	102254839
846	Elfis Hardi	Production Workshop	102254840
847	Athried Elsima	Warehouse	103254844
848	Guntoro	Production Workshop	102254854
849	Satrya Vasudeva Kresna	Production Workshop	102254873
850	Septian Mahendra	Production Workshop	102254877
851	Eko Wahyudi	Production Workshop	102254906
852	Naldiansyah Dalimunthe	Production Workshop	102254911
853	Lilis Dahlianis	Production	102254933
DATA;

        $lines = explode("\n", trim($rawData));
        $users = [];
        $password = Hash::make('STAFFKTU123'); // Hash once

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Robust Splitting: Split by whitespace to handle tabs converted to spaces
            $parts = preg_split('/\s+/', $line);
            if (count($parts) < 4) continue;

            // ID is first
            $id = array_shift($parts);
            // NIK is last
            $id_staff = array_pop($parts);

            // Middle is Name and Department
            $middle = implode(' ', $parts);
            
            $foundDept = null;
            $name = '';

            foreach ($departmentMap as $deptName => $deptId) {
                // Check if middle string ENDS with this department name
                // Use preg_quote to handle symbols e.g. 'Sales & Marketing'
                // Case insensitive check
                if (preg_match('/' . preg_quote($deptName, '/') . '$/i', $middle)) {
                    $foundDept = $deptName;
                    // Extract name: remove dept from end
                    $name = trim(substr($middle, 0, -strlen($deptName)));
                    break;
                }
            }

            // Fallback if not found (maybe department name has different casing or formatting?)
            if (!$foundDept) {
                // Try original tab split (if data is actually preserved)
                $tabParts = preg_split('/\t+/', $line);
                if (count($tabParts) >= 4) {
                    $name = trim($tabParts[1]);
                    $foundDept = trim($tabParts[2]);
                    if (!isset($departmentMap[$foundDept])) continue; // Unknown Dept
                } else {
                    continue; // Cannot identify dept
                }
            }

            $users[] = [
                'id' => (int) $id,
                'name' => $name,
                'email' => null,
                'department_id' => $departmentMap[$foundDept],
                'role' => 'user',
                'id_staff' => $id_staff,
                'password' => $password,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'location_id' => 3,
                'region_id' => 2,
            ];
        }

        // Use upsert to handle updates/duplicates
        if (count($users) > 0) {
            foreach (array_chunk($users, 200) as $chunk) {
                DB::table('users')->upsert($chunk, ['id'], ['name', 'department_id', 'id_staff']);
            }
        }
    }
}
