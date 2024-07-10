<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'name' => 'Ain',
            'code' => '01',
            'region_id' => 1
        ]);

        Department::create([
            'name' => 'Aisne',
            'code' => '02',
            'region_id' => 7
        ]);

        Department::create([
            'name' => 'Allier',
            'code' => '03',
            'region_id' => 1
        ]);

        Department::create([
            'name' => 'Alpes de Haute Provence',
            'code' => '04',
            'region_id' => 13
        ]);

        Department::create([
            'name' => 'Hautes Alpes',
            'code' => '05',
            'region_id' => 13
        ]);
        
        Department::create([
            'name' => 'Alpes Maritimes',
            'code' => '06',
            'region_id' => 13
        ]);
        
        Department::create([
            'name' => 'Ardèche',
            'code' => '07',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Ardennes',
            'code' => '08',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Ariège',
            'code' => '09',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Aube',
            'code' => '10',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Aude',
            'code' => '11',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Aveyron',
            'code' => '12',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Bouches du Rhône',
            'code' => '13',
            'region_id' => 13
        ]);
        
        Department::create([
            'name' => 'Calvados',
            'code' => '14',
            'region_id' => 9
        ]);
        
        Department::create([
            'name' => 'Cantal',
            'code' => '15',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Charente',
            'code' => '16',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Charente Maritime',
            'code' => '17',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Cher',
            'code' => '18',
            'region_id' => 4
        ]);
        
        Department::create([
            'name' => 'Corrèze',
            'code' => '19',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Corse du Sud',
            'code' => '2A',
            'region_id' => 5
        ]);
        
        Department::create([
            'name' => 'Haute Corse',
            'code' => '2B',
            'region_id' => 5
        ]);
        
        Department::create([
            'name' => 'Côte d\'Or',
            'code' => '21',
            'region_id' => 2
        ]);
        
        Department::create([
            'name' => 'Côtes d\'Armor',
            'code' => '22',
            'region_id' => 3
        ]);
        
        Department::create([
            'name' => 'Creuse',
            'code' => '23',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Dordogne',
            'code' => '24',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Doubs',
            'code' => '25',
            'region_id' => 2
        ]);
        
        Department::create([
            'name' => 'Drôme',
            'code' => '26',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Eure',
            'code' => '27',
            'region_id' => 9
        ]);
        
        Department::create([
            'name' => 'Eure et Loir',
            'code' => '28',
            'region_id' => 4
        ]);
        
        Department::create([
            'name' => 'Finistère',
            'code' => '29',
            'region_id' => 3
        ]);
        
        Department::create([
            'name' => 'Gard',
            'code' => '30',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Haute Garonne',
            'code' => '31',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Gers',
            'code' => '32',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Gironde',
            'code' => '33',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Hérault',
            'code' => '34',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Ille et Vilaine',
            'code' => '35',
            'region_id' => 3
        ]);
        
        Department::create([
            'name' => 'Indre',
            'code' => '36',
            'region_id' => 4
        ]);

        
        Department::create([
            'name' => 'Indre et Loire',
            'code' => '37',
            'region_id' => 4
        ]);
        
        Department::create([
            'name' => 'Isère',
            'code' => '38',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Jura',
            'code' => '39',
            'region_id' => 2
        ]);
        
        Department::create([
            'name' => 'Landes',
            'code' => '40',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Loir et Cher',
            'code' => '41',
            'region_id' => 4
        ]);
        
        Department::create([
            'name' => 'Loire',
            'code' => '42',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Haute Loire',
            'code' => '43',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Loire Atlantique',
            'code' => '44',
            'region_id' => 12
        ]);
        Department::create([
            'name' => 'Loiret',
            'code' => '45',
            'region_id' => 4
        ]);
        
        Department::create([
            'name' => 'Lot',
            'code' => '46',
            'region_id' => 11
        ]);
        Department::create([
            'name' => 'Lot et Garonne',
            'code' => '47',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Lozère',
            'code' => '48',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Maine et Loire',
            'code' => '49',
            'region_id' => 12
        ]);
        
        Department::create([
            'name' => 'Manche',
            'code' => '50',
            'region_id' => 9
        ]);
        
        Department::create([
            'name' => 'Marne',
            'code' => '51',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Haute Marne',
            'code' => '52',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Mayenne',
            'code' => '53',
            'region_id' => 12
        ]);
        
        Department::create([
            'name' => 'Meurthe et Moselle',
            'code' => '54',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Meuse',
            'code' => '55',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Morbihan',
            'code' => '56',
            'region_id' => 3
        ]);
        
        Department::create([
            'name' => 'Moselle',
            'code' => '57',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Nièvre',
            'code' => '58',
            'region_id' => 2
        ]);

        Department::create([
            'name' => 'Nord',
            'code' => '59',
            'region_id' => 7
        ]);

        Department::create([
            'name' => 'Oise',
            'code' => '60',
            'region_id' => 7
        ]);

        Department::create([
            'name' => 'Orne',
            'code' => '61',
            'region_id' => 9
        ]);

        Department::create([
            'name' => 'Pas de Calais',
            'code' => '62',
            'region_id' => 7
        ]);
        
        Department::create([
            'name' => 'Puy de Dôme',
            'code' => '63',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Pyrénées Atlantiques',
            'code' => '64',
            'region_id' => 10
        ]);
        
        Department::create([
            'name' => 'Hautes Pyrénées',
            'code' => '65',
            'region_id' => 11
        ]);
        
        Department::create([
            'name' => 'Pyrénées Orientales',
            'code' => '66',
            'region_id' => 11
        ]);

        Department::create([
            'name' => 'Bas Rhin',
            'code' => '67',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Haut Rhin',
            'code' => '68',
            'region_id' => 6
        ]);
        
        Department::create([
            'name' => 'Rhône',
            'code' => '69',
            'region_id' => 1
        ]);
        Department::create([
            'name' => 'Haute Saône',
            'code' => '70',
            'region_id' => 2
        ]);
        
        Department::create([
            'name' => 'Saône et Loire',
            'code' => '71',
            'region_id' => 2
        ]);
        
        Department::create([
            'name' => 'Sarthe',
            'code' => '72',
            'region_id' => 12
        ]);
        
        Department::create([
            'name' => 'Savoie',
            'code' => '73',
            'region_id' => 1
        ]);
        
        Department::create([
            'name' => 'Haute Savoie',
            'code' => '74',
            'region_id' => 1
        ]);
                
        Department::create([
            'name' => 'Paris',
            'code' => '75',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Seine Maritime',
            'code' => '76',
            'region_id' => 9
        ]);
                
        Department::create([
            'name' => 'Seine et Marne',
            'code' => '77',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Yvelines',
            'code' => '78',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Deux Sèvres',
            'code' => '79',
            'region_id' => 10
        ]);
                
        Department::create([
            'name' => 'Somme',
            'code' => '80',
            'region_id' => 7
        ]);
                
        Department::create([
            'name' => 'Tarn',
            'code' => '81',
            'region_id' => 11
        ]);
                
        Department::create([
            'name' => 'Tarn et Garonne',
            'code' => '82',
            'region_id' => 11
        ]);
                
        Department::create([
            'name' => 'Var',
            'code' => '83',
            'region_id' => 13
        ]);
                
        Department::create([
            'name' => 'Vaucluse',
            'code' => '84',
            'region_id' => 13
        ]);        
        Department::create([
            'name' => 'Vendée',
            'code' => '85',
            'region_id' => 12
        ]);
                
        Department::create([
            'name' => 'Vienne',
            'code' => '86',
            'region_id' => 10
        ]);
                
        Department::create([
            'name' => 'Haute Vienne',
            'code' => '87',
            'region_id' => 10
        ]);
                
        Department::create([
            'name' => 'Vosges',
            'code' => '88',
            'region_id' => 6
        ]);
                
        Department::create([
            'name' => 'Yonne',
            'code' => '89',
            'region_id' => 2
        ]);        
        Department::create([
            'name' => 'Territoire de Belfort',
            'code' => '90',
            'region_id' => 2
        ]);
                
        Department::create([
            'name' => 'Essonne',
            'code' => '91',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Hauts de Seine',
            'code' => '92',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Seine St Denis',
            'code' => '93',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Val de Marne',
            'code' => '94',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Val d\'Oise',
            'code' => '95',
            'region_id' => 8
        ]);
                
        Department::create([
            'name' => 'Guadeloupe',
            'code' => '971',
            'region_id' => 14
        ]);
                
        Department::create([
            'name' => 'Martinique',
            'code' => '972',
            'region_id' => 14
        ]);
                
        Department::create([
            'name' => 'Guyane',
            'code' => '973',
            'region_id' => 14
        ]);

        Department::create([
            'name' => 'Réunion',
            'code' => '974',
            'region_id' => 14
        ]);

        Department::create([
            'name' => 'Saint Pierre et Miquelon',
            'code' => '975',
            'region_id' => 14
        ]);

        Department::create([
            'name' => 'Mayotte',
            'code' => '976',
            'region_id' => 14
        ]);
    }
}
