<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // insertion de 10 Placex réels
        Place::create([
            'name' => 'Forêt de Mervent',
            'description' => 'La forêt de Mervent-Vouvant ou le massif forestier de 
            Mervent-Vouvant est une forêt située dans le département de la Vendée, 
            en région Pays de la Loire. Avec une superficie de 5 518 hectares, 
            c\'est le plus grand massif forestier de Vendée. (source : Wikipédia)',
            'latitude' => '46.5235',
            'longitude' => '-0.756878',
            'note' => '8.7',
            'time' => '4',
            'difficulty' => 'amateur',
            'adress' => 'Forêt Domaniale de Mervent-Vouvant',
            'postcode' => '85200',
            'city' => 'Mervent',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 2,
            'department_id' => 86
        ]);

        Place::create([
            'name' => 'Parc Ornithologique : Les Oiseaux du Marais Poitevin',
            'description' => 'Excursions guidées en barque sur des cours d\'eau pour observer des hérons, 
            film sur les oiseaux de la région et réserve de canards.',
            'latitude' => '46.275605',
            'longitude' => '-0.722044',
            'note' => '8.5',
            'time' => '3',
            'difficulty' => 'famille',
            'adress' => 'Le Petit Buisson',
            'postcode' => '79210',
            'city' => 'Saint-Hilaire-la-Palud',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 8,
            'department_id' => 80
        ]);

        Place::create([
            'name' => 'Jardin des Plantes',
            'latitude' => '47.220432',
            'description' => 'Avec 7 hectares de verdure en plein centre-city, plus de 10 000 espèces vivantes, 800 mètres carrés de serres et plus de 50 000 fleurs 
            plantées chaque saison, le Jardin des Plantes de Nantes, labellisé Jardin Remarquable, figure parmi les quatre grands jardins botaniques de France.
            En 150 ans, les collections se sont enrichies et spécialisées. Une partie de ces collections est cultivée dans d’élégantes serres ouvragées du XIXe siècle.   
            Il est ainsi possible de découvrir le jardin en autonameie (visite libre gratuite des serres avec code d’accès retiré à l’accueil ; jeu de piste gratuit avec 
            manipulation pour toute la famille) ou accompagné d’un jardinier botaniste (visites guidées gratuites).
            Chaque année le Jardin des Plantes accueille près de 2 millions de visiteurs qui viennent également s’y reposer des tumultes de la city, ou bien découvrir 
            le mobilier onirique et l’aire de jeux de Claude Ponti, ou encore profiter des animations, concerts et expositions proposées tout au long de l’année.
            (source: https://www.parcsetjardins.fr/jardins/719-jardin-des-plantes-de-nantes)',
            'longitude' => '-1.544265',
            'note' => '8.2',
            'time' => '2',
            'difficulty' => 'famille',
            'adress' => 'Rue Stanislas Baudry',
            'postcode' => '44000',
            'city' => 'Nantes',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 6,
            'department_id' => 45
        ]);

        Place::create([
            'name' => 'Zoo de la Palmyre',
            'description' => 'Le zoo de La Palmyre est un parc zoologique français situé sur la côte atlantique de la Nouvelle-Aquitaine, en Charente-Maritime, 
            dans la presqu\'île d\'Arvert, entre Royan et l\'Île d\'Oléron. Créé en 1966, il s\'étend sur 18 hectares au sein de la station balnéaire de La Palmyre, 
            enclavée entre la forêt domaniale de la Coubre et la forêt des Combots 
            d\'Ansoine. Le parc, aménagé en jardin paysager, offre au visiteur 
            l\'occasion d\'observer plus de 1 500 animaux de 115 espèces, tout au 
            long d\'un parcours de plus de 4 kilomètres. (source : Wikipédia)',
            'latitude' => '45.682261',
            'longitude' => '-1.167736',
            'note' => '9.1',
            'time' => '4',
            'difficulty' => 'famille',
            'adress' => '6 Av. de Royan',
            'postcode' => '17570',
            'city' => 'Les Mathes',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 8,
            'department_id' => 17
        ]);

        Place::create([
            'name' => 'Planète Sauvage',
            'description' => 'Planète Sauvage est un parc zoologique situé sur la côte atlantique française, à Port-Saint-Père, près de Nantes, dans le département 
            de Loire-Atlantique.
            Il couvre une superficie d\'environ quatre-vingt-cinq hectares, dans lesquels vivent environ 1 100 animaux de 120 espèces, et se compose d\'une partie 
            safari visitable en voiture et d\'une partie piétonne qui comprend notamment l\'un des deux delphinariums de France métropolitaine, où sont présentés
            des grands dauphins. (source : Wikipédia)',
            'latitude' => '47.116512',
            'longitude' => '-1.765285',
            'note' => '8.6',
            'time' => '6',
            'difficulty' => 'famille',
            'adress' => 'La Chevalerie',
            'postcode' => '44710',
            'city' => 'Port-Saint-Père',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 8,
            'department_id' => 45
        ]);

        Place::create([
            'name' => 'Parc naturel régional du Marais poitevin',
            'description' => 'Le marais poitevin s\'étend sur 3 départements (Vendée, Deux-Sèvres et Charente-Maritime) et 100 000 hectares ! Il est composé d\'une myriade de petits canaux sur lesquels on peut 
             naviguer en barque. On lui attribue d\'ailleurs le surname de "Venise Verte" à cause des lentilles qui recouvrent l\'eau.
             Il est également possible de s\'y promener à pied ou à vélo. Les plus jolis villages sont Arçais et Coulon, tous deux dotés d\'embarcadères.
             NB :l\'adress ci-dessous est celle du siège administratif du Parc.',
            'latitude' => '46.3220216',
            'longitude' => '-0.5859276',
            'note' => '9.3',
            'time' => '4',
            'difficulty' => 'famille',
            'adress' => '2 rue de l\'église',
            'postcode' => '79510',
            'city' => 'Coulon',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 5,
            'department_id' => 80
        ]);

        Place::create([
            'name' => 'Plage des Chardons',
            'description' => 'Grande plage de sable fin surveillée en Juillet et Août. Accès à pied par une pinède qui comporte des tables de pique-nique.
            Un endroit très sympathique pour une sortie plage en famille ou entre amis.',
            'latitude' => '46.323139',
            'longitude' => '-1.3229756',
            'note' => '8.5',
            'time' => '3',
            'difficulty' => 'famille',
            'adress' => 'Accès 12 - Boulevard de la Forêt',
            'postcode' => '85460',
            'city' => 'L\'AIGUILLON-LA-PRESQU\'ILE',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 1,
            'department_id' => 86
        ]);

        Place::create([
            'name' => 'Plage des Conches',
            'description' => ' La plage des Conches de Longecity sur Mer est la plus grande plage de la commune. Située dans le village des Conches, 
            on accède à la partie principale de la plage par l’avenue du Docteur Joussemet. En saison, cette zone est surveillée (accès n°13).
            Bordée par d’imposantes dunes, la plage devient rapidement sauvage en s’écartant de l’accès principal. A noter que dans la partie nord 
            on trouve plusieurs campings.
            En allant vers le sud, la plage change de name et porte le name de plage de Bud Bud. Cette zone est l’un des plus célèbres spots de 
            surf de la Vendée (souvent comparé aux meilleurs spots de la côte basque et des Landes). Il se trouve entre les accès 14 et 16 (accès par 
            le chemin de Bud mais impossible de stationner, il est conseillé de se garer au niveau du parking de l’accès 13 puis de marcher vers le sud 
            sur 600 mètres).(source : https://www.plages.tv/detail/plage-des-conches-bud-bud-longecity-sur-mer-85560)',
            'latitude' => '46.23334',
            'longitude' => '-1.296334',
            'note' => '8.1',
            'time' => '2',
            'difficulty' => 'famille',
            'adress' => 'Avenue du Docteur Joussemet',
            'postcode' => '85560',
            'city' => 'Longecity-sur-Mer',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 1,
            'department_id' => 86
        ]);

        Place::create([
            'name' => 'Parc de Pré Leroy',
            'description' => 'Le parc de Pré-Leroy est situé en centre-city, en bordure de Sèvre. Il s\'étend sur 2 hectares. 
            Il regroupe une aire de jeux pour les enfants, un parcours d’orientation, un skatepark et une piscine.
            On y accède par les Vieux-Ponts (entrée principale). (source : https://www.vivre-a-niort.com/cadre-de-vie/environnement/parcs-et-jardins/parc-de-pre-leroy/index.html)',
            'latitude' => ' 46.3300424',
            'longitude' => '-0.4632488',
            'note' => '7.9',
            'time' => '2',
            'difficulty' => 'famille',
            'adress' => 'Parc de Pré Leroy',
            'postcode' => '79000',
            'city' => 'Niort',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 6,
            'department_id' => 80
        ]);

        Place::create([
            'name' => 'La Vallée Des Singes',
            'description' => 'La Vallée des Singes est un parc zoologique français situé à Romagne, dans la Vienne. Fondé en 1998, il présente une 
            grande diversité de primates sur des îles végétalisées que le visiteur traverse en circulant au miPlace des singes 
            et autres lémuriens. (source : Wikipédia)',
            'latitude' => '46.2702',
            'longitude' => '0.30221',
            'note' => '7.5',
            'time' => '3',
            'difficulty' => 'famille',
            'adress' => 'Le Gureau',
            'postcode' => '86700',
            'city' => 'Romagne',
            'user_id' => 1,
            'status' => "validé",
            'category_id' => 8,
            'department_id' => 87
        ]);

        // insertion de 100 Placex aléatoires via la PlaceFactory (ids 11 à 110)
        \App\Models\Place::factory(100)->create();
    }
}
