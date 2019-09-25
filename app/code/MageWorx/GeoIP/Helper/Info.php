<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Helper;

/**
 * GeoIP Info helper
 */
class Info extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * To avoid inconsistency between Magento and Maxmind region names.
     * 01-11-2016 - http://geolite.maxmind.com/download/geoip/database/GeoLite2-City-CSV.zip
     *
     * @return array
     */
    public function getMaxmindData()
    {
        $data =
            [
                'AD' =>
                    [
                        'value' => 'AD',
                        'label' => 'Andorra',
                        'regions' =>
                            [
                                'Andorra la Vella' => 'Andorra la Vella',
                                'Canillo' => 'Canillo',
                                'Encamp' => 'Encamp',
                                'Escaldes-Engordany' => 'Escaldes-Engordany',
                                'La Massana' => 'La Massana',
                                'Ordino' => 'Ordino',
                                'Sant Julià de Loria' => 'Sant Julià de Loria',
                            ],
                    ],
                'AE' =>
                    [
                        'value' => 'AE',
                        'label' => 'United Arab Emirates',
                        'regions' =>
                            [
                                'Abu Dhabi' => 'Abu Dhabi',
                                'Ajman' => 'Ajman',
                                'Al Fujayrah' => 'Al Fujayrah',
                                'Ash Shariqah' => 'Ash Shariqah',
                                'Dubai' => 'Dubai',
                                'Ra\'s al Khaymah' => 'Ra\'s al Khaymah',
                            ],
                    ],
                'AF' =>
                    [
                        'value' => 'AF',
                        'label' => 'Afghanistan',
                        'regions' =>
                            [
                                'Herat' => 'Herat',
                                'Kabul' => 'Kabul',
                                'Kandahar' => 'Kandahar',
                                'Zabul' => 'Zabul',
                            ],
                    ],
                'AG' =>
                    [
                        'value' => 'AG',
                        'label' => 'Antigua and Barbuda',
                        'regions' =>
                            [
                                'Barbuda' => 'Barbuda',
                                'Parish of Saint George' => 'Parish of Saint George',
                                'Parish of Saint John' => 'Parish of Saint John',
                                'Parish of Saint Mary' => 'Parish of Saint Mary',
                                'Parish of Saint Paul' => 'Parish of Saint Paul',
                                'Parish of Saint Peter' => 'Parish of Saint Peter',
                                'Parish of Saint Philip' => 'Parish of Saint Philip',
                            ],
                    ],
                'AI' =>
                    [
                        'value' => 'AI',
                        'label' => 'Anguilla',
                        'regions' =>
                            [
                            ],
                    ],
                'AL' =>
                    [
                        'value' => 'AL',
                        'label' => 'Albania',
                        'regions' =>
                            [
                                'Qarku i Beratit' => 'Qarku i Beratit',
                                'Qarku i Dibres' => 'Qarku i Dibres',
                                'Qarku i Durresit' => 'Qarku i Durresit',
                                'Qarku i Elbasanit' => 'Qarku i Elbasanit',
                                'Qarku i Fierit' => 'Qarku i Fierit',
                                'Qarku i Gjirokastres' => 'Qarku i Gjirokastres',
                                'Qarku i Korces' => 'Qarku i Korces',
                                'Qarku i Kukesit' => 'Qarku i Kukesit',
                                'Qarku i Lezhes' => 'Qarku i Lezhes',
                                'Qarku i Shkodres' => 'Qarku i Shkodres',
                                'Qarku i Tiranes' => 'Qarku i Tiranes',
                                'Qarku i Vlores' => 'Qarku i Vlores',
                            ],
                    ],
                'AM' =>
                    [
                        'value' => 'AM',
                        'label' => 'Armenia',
                        'regions' =>
                            [
                                'Aragatsotn Province' => 'Aragatsotn Province',
                                'Ararat Province' => 'Ararat Province',
                                'Armavir Province' => 'Armavir Province',
                                'Gegharkunik Province' => 'Gegharkunik Province',
                                'Kotayk Province' => 'Kotayk Province',
                                'Lori Province' => 'Lori Province',
                                'Shirak Province' => 'Shirak Province',
                                'Syunik Province' => 'Syunik Province',
                                'Tavush Province' => 'Tavush Province',
                                'Yerevan' => 'Yerevan',
                            ],
                    ],
                'AO' =>
                    [
                        'value' => 'AO',
                        'label' => 'Angola',
                        'regions' =>
                            [
                                'Bengo Province' => 'Bengo Province',
                                'Benguela' => 'Benguela',
                                'Bíe' => 'Bíe',
                                'Cabinda' => 'Cabinda',
                                'Cuando Cobango' => 'Cuando Cobango',
                                'Cuanza Norte Province' => 'Cuanza Norte Province',
                                'Cunene Province' => 'Cunene Province',
                                'Huambo' => 'Huambo',
                                'Luanda Norte' => 'Luanda Norte',
                                'Luanda Province' => 'Luanda Province',
                                'Lunda Sul' => 'Lunda Sul',
                                'Malanje Province' => 'Malanje Province',
                                'Moxico' => 'Moxico',
                                'Namibe Province' => 'Namibe Province',
                                'Uíge' => 'Uíge',
                            ],
                    ],
                'AQ' =>
                    [
                        'value' => 'AQ',
                        'label' => 'Antarctica',
                        'regions' =>
                            [
                            ],
                    ],
                'AR' =>
                    [
                        'value' => 'AR',
                        'label' => 'Argentina',
                        'regions' =>
                            [
                                'Buenos Aires' => 'Buenos Aires',
                                'Buenos Aires F.D.' => 'Buenos Aires F.D.',
                                'Catamarca Province' => 'Catamarca Province',
                                'Chaco Province' => 'Chaco Province',
                                'Chubut Province' => 'Chubut Province',
                                'Cordoba Province' => 'Cordoba Province',
                                'Corrientes Province' => 'Corrientes Province',
                                'Entre Ríos Province' => 'Entre Ríos Province',
                                'Formosa Province' => 'Formosa Province',
                                'Jujuy Province' => 'Jujuy Province',
                                'La Pampa Province' => 'La Pampa Province',
                                'La Rioja Province' => 'La Rioja Province',
                                'Mendoza Province' => 'Mendoza Province',
                                'Misiones Province' => 'Misiones Province',
                                'Neuquén Province' => 'Neuquén Province',
                                'Río Negro Province' => 'Río Negro Province',
                                'Salta Province' => 'Salta Province',
                                'San Juan Province' => 'San Juan Province',
                                'San Luis Province' => 'San Luis Province',
                                'Santa Cruz Province' => 'Santa Cruz Province',
                                'Santa Fe Province' => 'Santa Fe Province',
                                'Santiago del Estero Province' => 'Santiago del Estero Province',
                                'Tierra del Fuego Province' => 'Tierra del Fuego Province',
                                'Tucumán Province' => 'Tucumán Province',
                            ],
                    ],
                'AS' =>
                    [
                        'value' => 'AS',
                        'label' => 'American Samoa',
                        'regions' =>
                            [
                                'Eastern District' => 'Eastern District',
                            ],
                    ],
                'AT' =>
                    [
                        'value' => 'AT',
                        'label' => 'Austria',
                        'regions' =>
                            [
                                'Burgenland' => 'Burgenland',
                                'Carinthia' => 'Carinthia',
                                'Lower Austria' => 'Lower Austria',
                                'Salzburg' => 'Salzburg',
                                'Styria' => 'Styria',
                                'Tyrol' => 'Tyrol',
                                'Upper Austria' => 'Upper Austria',
                                'Vienna' => 'Vienna',
                                'Vorarlberg' => 'Vorarlberg',
                            ],
                    ],
                'AU' =>
                    [
                        'value' => 'AU',
                        'label' => 'Australia',
                        'regions' =>
                            [
                                'Australian Capital Territory' => 'Australian Capital Territory',
                                'New South Wales' => 'New South Wales',
                                'Northern Territory' => 'Northern Territory',
                                'Queensland' => 'Queensland',
                                'South Australia' => 'South Australia',
                                'Tasmania' => 'Tasmania',
                                'Victoria' => 'Victoria',
                                'Western Australia' => 'Western Australia',
                            ],
                    ],
                'AW' =>
                    [
                        'value' => 'AW',
                        'label' => 'Aruba',
                        'regions' =>
                            [
                            ],
                    ],
                'AX' =>
                    [
                        'value' => 'AX',
                        'label' => 'Åland',
                        'regions' =>
                            [
                            ],
                    ],
                'AZ' =>
                    [
                        'value' => 'AZ',
                        'label' => 'Azerbaijan',
                        'regions' =>
                            [
                                'Baku City' => 'Baku City',
                                'Imishli Rayon' => 'Imishli Rayon',
                                'Nakhichevan' => 'Nakhichevan',
                                'Qusar Rayon' => 'Qusar Rayon',
                                'Sabirabad Rayon' => 'Sabirabad Rayon',
                                'Sumqayit City' => 'Sumqayit City',
                            ],
                    ],
                'BA' =>
                    [
                        'value' => 'BA',
                        'label' => 'Bosnia and Herzegovina',
                        'regions' =>
                            [
                                'Brčko' => 'Brčko',
                                'Federation of Bosnia and Herzegovina' => 'Federation of Bosnia and Herzegovina',
                                'Republic of Srspka' => 'Republic of Srspka',
                            ],
                    ],
                'BB' =>
                    [
                        'value' => 'BB',
                        'label' => 'Barbados',
                        'regions' =>
                            [
                                'Christ Church' => 'Christ Church',
                                'Saint Andrew' => 'Saint Andrew',
                                'Saint George' => 'Saint George',
                                'Saint James' => 'Saint James',
                                'Saint Joseph' => 'Saint Joseph',
                                'Saint Lucy' => 'Saint Lucy',
                                'Saint Michael' => 'Saint Michael',
                                'Saint Philip' => 'Saint Philip',
                                'Saint Thomas' => 'Saint Thomas',
                            ],
                    ],
                'BD' =>
                    [
                        'value' => 'BD',
                        'label' => 'Bangladesh',
                        'regions' =>
                            [
                                'Barisal Division' => 'Barisal Division',
                                'Chittagong' => 'Chittagong',
                                'Dhaka Division' => 'Dhaka Division',
                                'Khulna Division' => 'Khulna Division',
                                'Rajshahi Division' => 'Rajshahi Division',
                                'Rangpur Division' => 'Rangpur Division',
                                'Sylhet Division' => 'Sylhet Division',
                            ],
                    ],
                'BE' =>
                    [
                        'value' => 'BE',
                        'label' => 'Belgium',
                        'regions' =>
                            [
                                'Brussels Capital' => 'Brussels Capital',
                                'Flanders' => 'Flanders',
                                'Wallonia' => 'Wallonia',
                            ],
                    ],
                'BF' =>
                    [
                        'value' => 'BF',
                        'label' => 'Burkina Faso',
                        'regions' =>
                            [
                                'Cascades Region' => 'Cascades Region',
                                'Centre' => 'Centre',
                                'Hauts-Bassins' => 'Hauts-Bassins',
                            ],
                    ],
                'BG' =>
                    [
                        'value' => 'BG',
                        'label' => 'Bulgaria',
                        'regions' =>
                            [
                                'Blagoevgrad' => 'Blagoevgrad',
                                'Burgas' => 'Burgas',
                                'Gabrovo' => 'Gabrovo',
                                'Haskovo' => 'Haskovo',
                                'Lovech' => 'Lovech',
                                'Oblast Dobrich' => 'Oblast Dobrich',
                                'Oblast Kardzhali' => 'Oblast Kardzhali',
                                'Oblast Kyustendil' => 'Oblast Kyustendil',
                                'Oblast Montana' => 'Oblast Montana',
                                'Oblast Pleven' => 'Oblast Pleven',
                                'Oblast Razgrad' => 'Oblast Razgrad',
                                'Oblast Ruse' => 'Oblast Ruse',
                                'Oblast Shumen' => 'Oblast Shumen',
                                'Oblast Silistra' => 'Oblast Silistra',
                                'Oblast Sliven' => 'Oblast Sliven',
                                'Oblast Smolyan' => 'Oblast Smolyan',
                                'Oblast Stara Zagora' => 'Oblast Stara Zagora',
                                'Oblast Targovishte' => 'Oblast Targovishte',
                                'Oblast Veliko Tarnovo' => 'Oblast Veliko Tarnovo',
                                'Oblast Vidin' => 'Oblast Vidin',
                                'Oblast Vratsa' => 'Oblast Vratsa',
                                'Oblast Yambol' => 'Oblast Yambol',
                                'Pazardzhik' => 'Pazardzhik',
                                'Pernik' => 'Pernik',
                                'Plovdiv' => 'Plovdiv',
                                'Sofia Province' => 'Sofia Province',
                                'Sofia-Capital' => 'Sofia-Capital',
                                'Varna' => 'Varna',
                            ],
                    ],
                'BH' =>
                    [
                        'value' => 'BH',
                        'label' => 'Bahrain',
                        'regions' =>
                            [
                                'Central Governorate' => 'Central Governorate',
                                'Manama' => 'Manama',
                                'Muharraq' => 'Muharraq',
                                'Southern Governorate' => 'Southern Governorate',
                            ],
                    ],
                'BI' =>
                    [
                        'value' => 'BI',
                        'label' => 'Burundi',
                        'regions' =>
                            [
                                'Bujumbura Mairie Province' => 'Bujumbura Mairie Province',
                            ],
                    ],
                'BJ' =>
                    [
                        'value' => 'BJ',
                        'label' => 'Benin',
                        'regions' =>
                            [
                                'Atlantique Department' => 'Atlantique Department',
                                'Littoral' => 'Littoral',
                            ],
                    ],
                'BL' =>
                    [
                        'value' => 'BL',
                        'label' => 'Saint-Barthélemy',
                        'regions' =>
                            [
                            ],
                    ],
                'BM' =>
                    [
                        'value' => 'BM',
                        'label' => 'Bermuda',
                        'regions' =>
                            [
                                'Hamilton city' => 'Hamilton city',
                                'Saint George' => 'Saint George',
                                'Sandys Parish' => 'Sandys Parish',
                            ],
                    ],
                'BN' =>
                    [
                        'value' => 'BN',
                        'label' => 'Brunei',
                        'regions' =>
                            [
                                'Belait District' => 'Belait District',
                                'Brunei and Muara District' => 'Brunei and Muara District',
                                'Temburong District' => 'Temburong District',
                                'Tutong District' => 'Tutong District',
                            ],
                    ],
                'BO' =>
                    [
                        'value' => 'BO',
                        'label' => 'Bolivia',
                        'regions' =>
                            [
                                'Departamento de Chuquisaca' => 'Departamento de Chuquisaca',
                                'Departamento de Cochabamba' => 'Departamento de Cochabamba',
                                'Departamento de La Paz' => 'Departamento de La Paz',
                                'Departamento de Pando' => 'Departamento de Pando',
                                'Departamento de Potosi' => 'Departamento de Potosi',
                                'Departamento de Santa Cruz' => 'Departamento de Santa Cruz',
                                'Departamento de Tarija' => 'Departamento de Tarija',
                                'El Beni' => 'El Beni',
                                'Oruro' => 'Oruro',
                            ],
                    ],
                'BQ' =>
                    [
                        'value' => 'BQ',
                        'label' => 'Bonaire, Sint Eustatius, and Saba',
                        'regions' =>
                            [
                                'Bonaire' => 'Bonaire',
                                'Saba' => 'Saba',
                            ],
                    ],
                'BR' =>
                    [
                        'value' => 'BR',
                        'label' => 'Brazil',
                        'regions' =>
                            [
                                'Acre' => 'Acre',
                                'Alagoas' => 'Alagoas',
                                'Amapa' => 'Amapa',
                                'Amazonas' => 'Amazonas',
                                'Bahia' => 'Bahia',
                                'Ceara' => 'Ceara',
                                'Espirito Santo' => 'Espirito Santo',
                                'Federal District' => 'Federal District',
                                'Goias' => 'Goias',
                                'Maranhao' => 'Maranhao',
                                'Mato Grosso' => 'Mato Grosso',
                                'Mato Grosso do Sul' => 'Mato Grosso do Sul',
                                'Minas Gerais' => 'Minas Gerais',
                                'Para' => 'Para',
                                'Parana' => 'Parana',
                                'Paraíba' => 'Paraíba',
                                'Pernambuco' => 'Pernambuco',
                                'Piaui' => 'Piaui',
                                'Rio Grande do Norte' => 'Rio Grande do Norte',
                                'Rio Grande do Sul' => 'Rio Grande do Sul',
                                'Rio de Janeiro' => 'Rio de Janeiro',
                                'Rondonia' => 'Rondonia',
                                'Roraima' => 'Roraima',
                                'Santa Catarina' => 'Santa Catarina',
                                'Sao Paulo' => 'Sao Paulo',
                                'Sergipe' => 'Sergipe',
                                'Tocantins' => 'Tocantins',
                            ],
                    ],
                'BS' =>
                    [
                        'value' => 'BS',
                        'label' => 'Bahamas',
                        'regions' =>
                            [
                                'Bimini' => 'Bimini',
                                'Central Abaco District' => 'Central Abaco District',
                                'City of Freeport District' => 'City of Freeport District',
                                'Harbour Island' => 'Harbour Island',
                                'New Providence District' => 'New Providence District',
                                'North Andros District' => 'North Andros District',
                                'North Eleuthera' => 'North Eleuthera',
                            ],
                    ],
                'BT' =>
                    [
                        'value' => 'BT',
                        'label' => 'Bhutan',
                        'regions' =>
                            [
                                'Chukha District' => 'Chukha District',
                                'Mongar District' => 'Mongar District',
                                'Thimphu Dzongkhag' => 'Thimphu Dzongkhag',
                            ],
                    ],
                'BW' =>
                    [
                        'value' => 'BW',
                        'label' => 'Botswana',
                        'regions' =>
                            [
                                'Central District' => 'Central District',
                                'Kweneng District' => 'Kweneng District',
                                'North-East' => 'North-East',
                                'North-West' => 'North-West',
                                'South-East' => 'South-East',
                            ],
                    ],
                'BY' =>
                    [
                        'value' => 'BY',
                        'label' => 'Belarus',
                        'regions' =>
                            [
                                'Brest' => 'Brest',
                                'Gomel' => 'Gomel',
                                'Grodnenskaya' => 'Grodnenskaya',
                                'Minsk' => 'Minsk',
                                'Minsk City' => 'Minsk City',
                                'Mogilev' => 'Mogilev',
                                'Vitebsk' => 'Vitebsk',
                            ],
                    ],
                'BZ' =>
                    [
                        'value' => 'BZ',
                        'label' => 'Belize',
                        'regions' =>
                            [
                                'Belize District' => 'Belize District',
                                'Cayo District' => 'Cayo District',
                                'Corozal District' => 'Corozal District',
                                'Orange Walk District' => 'Orange Walk District',
                                'Stann Creek District' => 'Stann Creek District',
                                'Toledo District' => 'Toledo District',
                            ],
                    ],
                'CA' =>
                    [
                        'value' => 'CA',
                        'label' => 'Canada',
                        'regions' =>
                            [
                                'Alberta' => 'Alberta',
                                'British Columbia' => 'British Columbia',
                                'Manitoba' => 'Manitoba',
                                'New Brunswick' => 'New Brunswick',
                                'Newfoundland and Labrador' => 'Newfoundland and Labrador',
                                'Northwest Territories' => 'Northwest Territories',
                                'Nova Scotia' => 'Nova Scotia',
                                'Nunavut' => 'Nunavut',
                                'Ontario' => 'Ontario',
                                'Prince Edward Island' => 'Prince Edward Island',
                                'Quebec' => 'Quebec',
                                'Saskatchewan' => 'Saskatchewan',
                                'Yukon' => 'Yukon',
                            ],
                    ],
                'CC' =>
                    [
                        'value' => 'CC',
                        'label' => 'Cocos [Keeling] Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'CD' =>
                    [
                        'value' => 'CD',
                        'label' => 'Congo',
                        'regions' =>
                            [
                                'Bas-Congo' => 'Bas-Congo',
                                'Katanga Province' => 'Katanga Province',
                                'Kinshasa City' => 'Kinshasa City',
                                'Nord Kivu' => 'Nord Kivu',
                                'South Kivu Province' => 'South Kivu Province',
                            ],
                    ],
                'CF' =>
                    [
                        'value' => 'CF',
                        'label' => 'Central African Republic',
                        'regions' =>
                            [
                                'Bangui' => 'Bangui',
                                'Mbomou' => 'Mbomou',
                            ],
                    ],
                'CG' =>
                    [
                        'value' => 'CG',
                        'label' => 'Republic of the Congo',
                        'regions' =>
                            [
                                'Brazzaville' => 'Brazzaville',
                                'Pointe-Noire' => 'Pointe-Noire',
                                'Sangha' => 'Sangha',
                            ],
                    ],
                'CH' =>
                    [
                        'value' => 'CH',
                        'label' => 'Switzerland',
                        'regions' =>
                            [
                                'Aargau' => 'Aargau',
                                'Appenzell Ausserrhoden' => 'Appenzell Ausserrhoden',
                                'Appenzell Innerrhoden' => 'Appenzell Innerrhoden',
                                'Basel-City' => 'Basel-City',
                                'Basel-Landschaft' => 'Basel-Landschaft',
                                'Bern' => 'Bern',
                                'Fribourg' => 'Fribourg',
                                'Geneva' => 'Geneva',
                                'Glarus' => 'Glarus',
                                'Grisons' => 'Grisons',
                                'Jura' => 'Jura',
                                'Lucerne' => 'Lucerne',
                                'Neuchâtel' => 'Neuchâtel',
                                'Nidwalden' => 'Nidwalden',
                                'Obwalden' => 'Obwalden',
                                'Saint Gallen' => 'Saint Gallen',
                                'Schaffhausen' => 'Schaffhausen',
                                'Schwyz' => 'Schwyz',
                                'Solothurn' => 'Solothurn',
                                'Thurgau' => 'Thurgau',
                                'Ticino' => 'Ticino',
                                'Uri' => 'Uri',
                                'Valais' => 'Valais',
                                'Vaud' => 'Vaud',
                                'Zug' => 'Zug',
                                'Zurich' => 'Zurich',
                            ],
                    ],
                'CI' =>
                    [
                        'value' => 'CI',
                        'label' => 'Ivory Coast',
                        'regions' =>
                            [
                                'Abidjan' => 'Abidjan',
                                'Comoe' => 'Comoe',
                                'District des Montagnes' => 'District des Montagnes',
                                'Sassandra-Marahoue' => 'Sassandra-Marahoue',
                            ],
                    ],
                'CK' =>
                    [
                        'value' => 'CK',
                        'label' => 'Cook Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'CL' =>
                    [
                        'value' => 'CL',
                        'label' => 'Chile',
                        'regions' =>
                            [
                                'Antofagasta' => 'Antofagasta',
                                'Atacama' => 'Atacama',
                                'Aysen' => 'Aysen',
                                'Coquimbo' => 'Coquimbo',
                                'Los Lagos' => 'Los Lagos',
                                'Maule' => 'Maule',
                                'Region de Arica y Parinacota' => 'Region de Arica y Parinacota',
                                'Region de Los Rios' => 'Region de Los Rios',
                                'Region de Magallanes y de la Antartica Chilena' => 'Region de Magallanes y de la Antartica Chilena',
                                'Region de Valparaiso' => 'Region de Valparaiso',
                                'Region de la Araucania' => 'Region de la Araucania',
                                'Region del Biobio' => 'Region del Biobio',
                                'Region del Libertador General Bernardo O\'Higgins' => 'Region del Libertador General Bernardo O\'Higgins',
                                'Santiago Metropolitan' => 'Santiago Metropolitan',
                                'Tarapacá' => 'Tarapacá',
                            ],
                    ],
                'CM' =>
                    [
                        'value' => 'CM',
                        'label' => 'Cameroon',
                        'regions' =>
                            [
                                'Adamaoua Region' => 'Adamaoua Region',
                                'Centre' => 'Centre',
                                'Littoral' => 'Littoral',
                                'North Region' => 'North Region',
                                'North-West Region' => 'North-West Region',
                                'South' => 'South',
                                'South-West Region' => 'South-West Region',
                                'West Region' => 'West Region',
                            ],
                    ],
                'CN' =>
                    [
                        'value' => 'CN',
                        'label' => 'China',
                        'regions' =>
                            [
                                'Anhui' => 'Anhui',
                                'Beijing' => 'Beijing',
                                'Chongqing' => 'Chongqing',
                                'Fujian' => 'Fujian',
                                'Gansu' => 'Gansu',
                                'Guangdong' => 'Guangdong',
                                'Guangxi Zhuang Autonomous Region' => 'Guangxi Zhuang Autonomous Region',
                                'Guizhou' => 'Guizhou',
                                'Hainan' => 'Hainan',
                                'Hebei' => 'Hebei',
                                'Heilongjiang' => 'Heilongjiang',
                                'Henan' => 'Henan',
                                'Hubei' => 'Hubei',
                                'Hunan' => 'Hunan',
                                'Inner Mongolia Autonomous Region' => 'Inner Mongolia Autonomous Region',
                                'Jiangsu' => 'Jiangsu',
                                'Jiangxi' => 'Jiangxi',
                                'Jilin' => 'Jilin',
                                'Liaoning' => 'Liaoning',
                                'Ningsia Hui Autonomous Region' => 'Ningsia Hui Autonomous Region',
                                'Qinghai' => 'Qinghai',
                                'Shaanxi' => 'Shaanxi',
                                'Shandong' => 'Shandong',
                                'Shanghai' => 'Shanghai',
                                'Shanxi' => 'Shanxi',
                                'Sichuan' => 'Sichuan',
                                'Tianjin' => 'Tianjin',
                                'Tibet Autonomous Region' => 'Tibet Autonomous Region',
                                'Xinjiang Uyghur Autonomous Region' => 'Xinjiang Uyghur Autonomous Region',
                                'Yunnan' => 'Yunnan',
                                'Zhejiang' => 'Zhejiang',
                            ],
                    ],
                'CO' =>
                    [
                        'value' => 'CO',
                        'label' => 'Colombia',
                        'regions' =>
                            [
                                'Amazonas' => 'Amazonas',
                                'Antioquia' => 'Antioquia',
                                'Atlántico' => 'Atlántico',
                                'Bogota D.C.' => 'Bogota D.C.',
                                'Cundinamarca' => 'Cundinamarca',
                                'Departamento de Bolivar' => 'Departamento de Bolivar',
                                'Departamento de Boyaca' => 'Departamento de Boyaca',
                                'Departamento de Caldas' => 'Departamento de Caldas',
                                'Departamento de Casanare' => 'Departamento de Casanare',
                                'Departamento de Cordoba' => 'Departamento de Cordoba',
                                'Departamento de La Guajira' => 'Departamento de La Guajira',
                                'Departamento de Narino' => 'Departamento de Narino',
                                'Departamento de Norte de Santander' => 'Departamento de Norte de Santander',
                                'Departamento de Risaralda' => 'Departamento de Risaralda',
                                'Departamento de Santander' => 'Departamento de Santander',
                                'Departamento de Sucre' => 'Departamento de Sucre',
                                'Departamento de Tolima' => 'Departamento de Tolima',
                                'Departamento del Caqueta' => 'Departamento del Caqueta',
                                'Departamento del Cauca' => 'Departamento del Cauca',
                                'Departamento del Cesar' => 'Departamento del Cesar',
                                'Departamento del Choco' => 'Departamento del Choco',
                                'Departamento del Guainia' => 'Departamento del Guainia',
                                'Departamento del Guaviare' => 'Departamento del Guaviare',
                                'Departamento del Huila' => 'Departamento del Huila',
                                'Departamento del Magdalena' => 'Departamento del Magdalena',
                                'Departamento del Meta' => 'Departamento del Meta',
                                'Departamento del Valle del Cauca' => 'Departamento del Valle del Cauca',
                                'Departamento del Vichada' => 'Departamento del Vichada',
                                'Providencia y Santa Catalina, Departamento de Archipielago de San Andres' => 'Providencia y Santa Catalina, Departamento de Archipielago de San Andres',
                                'Quindio Department' => 'Quindio Department',
                            ],
                    ],
                'CR' =>
                    [
                        'value' => 'CR',
                        'label' => 'Costa Rica',
                        'regions' =>
                            [
                                'Provincia de Alajuela' => 'Provincia de Alajuela',
                                'Provincia de Cartago' => 'Provincia de Cartago',
                                'Provincia de Guanacaste' => 'Provincia de Guanacaste',
                                'Provincia de Heredia' => 'Provincia de Heredia',
                                'Provincia de Limon' => 'Provincia de Limon',
                                'Provincia de Puntarenas' => 'Provincia de Puntarenas',
                                'Provincia de San Jose' => 'Provincia de San Jose',
                            ],
                    ],
                'CU' =>
                    [
                        'value' => 'CU',
                        'label' => 'Cuba',
                        'regions' =>
                            [
                                'La Habana' => 'La Habana',
                                'Provincia de Camagueey' => 'Provincia de Camagueey',
                                'Provincia de Ciego de Avila' => 'Provincia de Ciego de Avila',
                                'Provincia de Matanzas' => 'Provincia de Matanzas',
                                'Provincia de Villa Clara' => 'Provincia de Villa Clara',
                            ],
                    ],
                'CV' =>
                    [
                        'value' => 'CV',
                        'label' => 'Cape Verde',
                        'regions' =>
                            [
                                'Ribeira Grande' => 'Ribeira Grande',
                                'Ribeira Grande de Santiago' => 'Ribeira Grande de Santiago',
                                'São Domingos' => 'São Domingos',
                            ],
                    ],
                'CW' =>
                    [
                        'value' => 'CW',
                        'label' => 'Curaçao',
                        'regions' =>
                            [
                            ],
                    ],
                'CX' =>
                    [
                        'value' => 'CX',
                        'label' => 'Christmas Island',
                        'regions' =>
                            [
                            ],
                    ],
                'CY' =>
                    [
                        'value' => 'CY',
                        'label' => 'Cyprus',
                        'regions' =>
                            [
                                'Ammochostos' => 'Ammochostos',
                                'Keryneia' => 'Keryneia',
                                'Larnaka' => 'Larnaka',
                                'Limassol' => 'Limassol',
                                'Nicosia' => 'Nicosia',
                                'Pafos' => 'Pafos',
                            ],
                    ],
                'CZ' =>
                    [
                        'value' => 'CZ',
                        'label' => 'Czechia',
                        'regions' =>
                            [
                                'Central Bohemia' => 'Central Bohemia',
                                'Hlavni mesto Praha' => 'Hlavni mesto Praha',
                                'Jihocesky kraj' => 'Jihocesky kraj',
                                'Karlovarsky kraj' => 'Karlovarsky kraj',
                                'Kraj Vysocina' => 'Kraj Vysocina',
                                'Kralovehradecky kraj' => 'Kralovehradecky kraj',
                                'Liberecky kraj' => 'Liberecky kraj',
                                'Moravskoslezsky kraj' => 'Moravskoslezsky kraj',
                                'Olomoucky kraj' => 'Olomoucky kraj',
                                'Pardubicky kraj' => 'Pardubicky kraj',
                                'Plzensky kraj' => 'Plzensky kraj',
                                'South Moravian' => 'South Moravian',
                                'Ustecky kraj' => 'Ustecky kraj',
                                'Zlín' => 'Zlín',
                            ],
                    ],
                'DE' =>
                    [
                        'value' => 'DE',
                        'label' => 'Germany',
                        'regions' =>
                            [
                                'Baden-Württemberg Region' => 'Baden-Württemberg Region',
                                'Bavaria' => 'Bavaria',
                                'Brandenburg' => 'Brandenburg',
                                'Bremen' => 'Bremen',
                                'Hamburg' => 'Hamburg',
                                'Hesse' => 'Hesse',
                                'Land Berlin' => 'Land Berlin',
                                'Lower Saxony' => 'Lower Saxony',
                                'Mecklenburg-Vorpommern' => 'Mecklenburg-Vorpommern',
                                'North Rhine-Westphalia' => 'North Rhine-Westphalia',
                                'Rheinland-Pfalz' => 'Rheinland-Pfalz',
                                'Saarland' => 'Saarland',
                                'Saxony' => 'Saxony',
                                'Saxony-Anhalt' => 'Saxony-Anhalt',
                                'Schleswig-Holstein' => 'Schleswig-Holstein',
                                'Thuringia' => 'Thuringia',
                            ],
                    ],
                'DJ' =>
                    [
                        'value' => 'DJ',
                        'label' => 'Djibouti',
                        'regions' =>
                            [
                            ],
                    ],
                'DK' =>
                    [
                        'value' => 'DK',
                        'label' => 'Denmark',
                        'regions' =>
                            [
                                'Capital Region' => 'Capital Region',
                                'Central Jutland' => 'Central Jutland',
                                'North Denmark' => 'North Denmark',
                                'South Denmark' => 'South Denmark',
                                'Zealand' => 'Zealand',
                            ],
                    ],
                'DM' =>
                    [
                        'value' => 'DM',
                        'label' => 'Dominica',
                        'regions' =>
                            [
                                'Saint Andrew' => 'Saint Andrew',
                                'Saint David' => 'Saint David',
                                'Saint George' => 'Saint George',
                                'Saint John' => 'Saint John',
                                'Saint Patrick' => 'Saint Patrick',
                                'Saint Paul' => 'Saint Paul',
                            ],
                    ],
                'DO' =>
                    [
                        'value' => 'DO',
                        'label' => 'Dominican Republic',
                        'regions' =>
                            [
                                'Nacional' => 'Nacional',
                                'Provincia Duarte' => 'Provincia Duarte',
                                'Provincia Espaillat' => 'Provincia Espaillat',
                                'Provincia Sanchez Ramirez' => 'Provincia Sanchez Ramirez',
                                'Provincia de Barahona' => 'Provincia de Barahona',
                                'Provincia de El Seibo' => 'Provincia de El Seibo',
                                'Provincia de Hato Mayor' => 'Provincia de Hato Mayor',
                                'Provincia de Hermanas Mirabal' => 'Provincia de Hermanas Mirabal',
                                'Provincia de Independencia' => 'Provincia de Independencia',
                                'Provincia de La Altagracia' => 'Provincia de La Altagracia',
                                'Provincia de La Romana' => 'Provincia de La Romana',
                                'Provincia de La Vega' => 'Provincia de La Vega',
                                'Provincia de Monsenor Nouel' => 'Provincia de Monsenor Nouel',
                                'Provincia de Monte Cristi' => 'Provincia de Monte Cristi',
                                'Provincia de Monte Plata' => 'Provincia de Monte Plata',
                                'Provincia de Pedernales' => 'Provincia de Pedernales',
                                'Provincia de Peravia' => 'Provincia de Peravia',
                                'Provincia de San Cristobal' => 'Provincia de San Cristobal',
                                'Provincia de San Jose de Ocoa' => 'Provincia de San Jose de Ocoa',
                                'Provincia de San Juan' => 'Provincia de San Juan',
                                'Provincia de San Pedro de Macoris' => 'Provincia de San Pedro de Macoris',
                                'Provincia de Santiago' => 'Provincia de Santiago',
                                'Provincia de Santiago Rodriguez' => 'Provincia de Santiago Rodriguez',
                                'Provincia de Santo Domingo' => 'Provincia de Santo Domingo',
                                'Puerto Plata' => 'Puerto Plata',
                            ],
                    ],
                'DZ' =>
                    [
                        'value' => 'DZ',
                        'label' => 'Algeria',
                        'regions' =>
                            [
                                'Adrar' => 'Adrar',
                                'Algiers' => 'Algiers',
                                'Annaba' => 'Annaba',
                                'Aïn Defla' => 'Aïn Defla',
                                'Aïn Témouchent' => 'Aïn Témouchent',
                                'Batna' => 'Batna',
                                'Biskra' => 'Biskra',
                                'Blida' => 'Blida',
                                'Bouira' => 'Bouira',
                                'Boumerdes' => 'Boumerdes',
                                'Béchar' => 'Béchar',
                                'Béjaïa' => 'Béjaïa',
                                'Chlef' => 'Chlef',
                                'Constantine' => 'Constantine',
                                'Djelfa' => 'Djelfa',
                                'El Bayadh' => 'El Bayadh',
                                'El Tarf' => 'El Tarf',
                                'Ghardaia' => 'Ghardaia',
                                'Guelma' => 'Guelma',
                                'Illizi' => 'Illizi',
                                'Jijel' => 'Jijel',
                                'Khenchela' => 'Khenchela',
                                'Laghouat' => 'Laghouat',
                                'M\'Sila' => 'M\'Sila',
                                'Mascara' => 'Mascara',
                                'Medea' => 'Medea',
                                'Mila' => 'Mila',
                                'Mostaganem' => 'Mostaganem',
                                'Naama' => 'Naama',
                                'Oran' => 'Oran',
                                'Ouargla' => 'Ouargla',
                                'Oum el Bouaghi' => 'Oum el Bouaghi',
                                'Relizane' => 'Relizane',
                                'Saida' => 'Saida',
                                'Sidi Bel Abbès' => 'Sidi Bel Abbès',
                                'Skikda' => 'Skikda',
                                'Sétif' => 'Sétif',
                                'Tamanrasset' => 'Tamanrasset',
                                'Tiaret' => 'Tiaret',
                                'Tindouf' => 'Tindouf',
                                'Tipaza' => 'Tipaza',
                                'Tissemsilt' => 'Tissemsilt',
                                'Tizi Ouzou' => 'Tizi Ouzou',
                                'Tlemcen' => 'Tlemcen',
                            ],
                    ],
                'EC' =>
                    [
                        'value' => 'EC',
                        'label' => 'Ecuador',
                        'regions' =>
                            [
                                'Provincia de Bolivar' => 'Provincia de Bolivar',
                                'Provincia de Cotopaxi' => 'Provincia de Cotopaxi',
                                'Provincia de El Oro' => 'Provincia de El Oro',
                                'Provincia de Esmeraldas' => 'Provincia de Esmeraldas',
                                'Provincia de Francisco de Orellana' => 'Provincia de Francisco de Orellana',
                                'Provincia de Imbabura' => 'Provincia de Imbabura',
                                'Provincia de Loja' => 'Provincia de Loja',
                                'Provincia de Los Rios' => 'Provincia de Los Rios',
                                'Provincia de Manabi' => 'Provincia de Manabi',
                                'Provincia de Morona-Santiago' => 'Provincia de Morona-Santiago',
                                'Provincia de Napo' => 'Provincia de Napo',
                                'Provincia de Pichincha' => 'Provincia de Pichincha',
                                'Provincia de Santa Elena' => 'Provincia de Santa Elena',
                                'Provincia de Santo Domingo de los Tsachilas' => 'Provincia de Santo Domingo de los Tsachilas',
                                'Provincia de Sucumbios' => 'Provincia de Sucumbios',
                                'Provincia de Zamora-Chinchipe' => 'Provincia de Zamora-Chinchipe',
                                'Provincia del Azuay' => 'Provincia del Azuay',
                                'Provincia del Canar' => 'Provincia del Canar',
                                'Provincia del Carchi' => 'Provincia del Carchi',
                                'Provincia del Chimborazo' => 'Provincia del Chimborazo',
                                'Provincia del Guayas' => 'Provincia del Guayas',
                                'Provincia del Pastaza' => 'Provincia del Pastaza',
                                'Provincia del Tungurahua' => 'Provincia del Tungurahua',
                            ],
                    ],
                'EE' =>
                    [
                        'value' => 'EE',
                        'label' => 'Estonia',
                        'regions' =>
                            [
                                'Harjumaa' => 'Harjumaa',
                                'Hiiumaa' => 'Hiiumaa',
                                'Ida-Virumaa' => 'Ida-Virumaa',
                                'Järvamaa' => 'Järvamaa',
                                'Jõgevamaa' => 'Jõgevamaa',
                                'Lääne' => 'Lääne',
                                'Lääne-Virumaa' => 'Lääne-Virumaa',
                                'Pärnumaa' => 'Pärnumaa',
                                'Põlvamaa' => 'Põlvamaa',
                                'Raplamaa' => 'Raplamaa',
                                'Saare' => 'Saare',
                                'Tartu' => 'Tartu',
                                'Valgamaa' => 'Valgamaa',
                                'Viljandimaa' => 'Viljandimaa',
                                'Võrumaa' => 'Võrumaa',
                            ],
                    ],
                'EG' =>
                    [
                        'value' => 'EG',
                        'label' => 'Egypt',
                        'regions' =>
                            [
                                'Alexandria' => 'Alexandria',
                                'Aswan' => 'Aswan',
                                'Asyut' => 'Asyut',
                                'Beheira' => 'Beheira',
                                'Beni Suweif' => 'Beni Suweif',
                                'Cairo Governorate' => 'Cairo Governorate',
                                'Dakahlia' => 'Dakahlia',
                                'Damietta Governorate' => 'Damietta Governorate',
                                'Faiyum' => 'Faiyum',
                                'Gharbia' => 'Gharbia',
                                'Giza' => 'Giza',
                                'Ismailia Governorate' => 'Ismailia Governorate',
                                'Kafr el-Sheikh' => 'Kafr el-Sheikh',
                                'Luxor' => 'Luxor',
                                'Minya' => 'Minya',
                                'Monufia' => 'Monufia',
                                'North Sinai' => 'North Sinai',
                                'Port Said' => 'Port Said',
                                'Qalyubia' => 'Qalyubia',
                                'Qena' => 'Qena',
                                'Red Sea' => 'Red Sea',
                                'Sharqia' => 'Sharqia',
                                'Sohag' => 'Sohag',
                                'Suez' => 'Suez',
                            ],
                    ],
                'ER' =>
                    [
                        'value' => 'ER',
                        'label' => 'Eritrea',
                        'regions' =>
                            [
                                'Maekel Region' => 'Maekel Region',
                            ],
                    ],
                'ES' =>
                    [
                        'value' => 'ES',
                        'label' => 'Spain',
                        'regions' =>
                            [
                                'Andalusia' => 'Andalusia',
                                'Aragon' => 'Aragon',
                                'Balearic Islands' => 'Balearic Islands',
                                'Basque Country' => 'Basque Country',
                                'Canary Islands' => 'Canary Islands',
                                'Cantabria' => 'Cantabria',
                                'Castille and León' => 'Castille and León',
                                'Castille-La Mancha' => 'Castille-La Mancha',
                                'Catalonia' => 'Catalonia',
                                'Ceuta' => 'Ceuta',
                                'Extremadura' => 'Extremadura',
                                'Galicia' => 'Galicia',
                                'La Rioja' => 'La Rioja',
                                'Madrid' => 'Madrid',
                                'Melilla' => 'Melilla',
                                'Murcia' => 'Murcia',
                                'Navarre' => 'Navarre',
                                'Principality of Asturias' => 'Principality of Asturias',
                                'Valencia' => 'Valencia',
                            ],
                    ],
                'ET' =>
                    [
                        'value' => 'ET',
                        'label' => 'Ethiopia',
                        'regions' =>
                            [
                                'Addis Ababa' => 'Addis Ababa',
                                'Afar Region' => 'Afar Region',
                                'Amhara' => 'Amhara',
                                'Bīnshangul Gumuz' => 'Bīnshangul Gumuz',
                                'Dire Dawa' => 'Dire Dawa',
                                'Gambela' => 'Gambela',
                                'Harari Region' => 'Harari Region',
                                'Oromiya' => 'Oromiya',
                                'Somali' => 'Somali',
                                'Southern Nations, Nationalities, and People\'s Region' => 'Southern Nations, Nationalities, and People\'s Region',
                                'Tigray' => 'Tigray',
                            ],
                    ],
                'FI' =>
                    [
                        'value' => 'FI',
                        'label' => 'Finland',
                        'regions' =>
                            [
                                'Central Finland' => 'Central Finland',
                                'Central Ostrobothnia' => 'Central Ostrobothnia',
                                'Haeme' => 'Haeme',
                                'Kainuu' => 'Kainuu',
                                'Kymenlaakso' => 'Kymenlaakso',
                                'Lapland' => 'Lapland',
                                'Lapponia' => 'Lapponia',
                                'North Karelia' => 'North Karelia',
                                'Northern Ostrobothnia' => 'Northern Ostrobothnia',
                                'Northern Savo' => 'Northern Savo',
                                'Päijänne Tavastia' => 'Päijänne Tavastia',
                                'Satakunta' => 'Satakunta',
                                'South Karelia' => 'South Karelia',
                                'Southern Ostrobothnia' => 'Southern Ostrobothnia',
                                'Southern Savonia' => 'Southern Savonia',
                                'Southwest Finland' => 'Southwest Finland',
                                'Uusimaa' => 'Uusimaa',
                                'Western Finland' => 'Western Finland',
                            ],
                    ],
                'FJ' =>
                    [
                        'value' => 'FJ',
                        'label' => 'Fiji',
                        'regions' =>
                            [
                                'Central' => 'Central',
                                'Western' => 'Western',
                            ],
                    ],
                'FK' =>
                    [
                        'value' => 'FK',
                        'label' => 'Falkland Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'FM' =>
                    [
                        'value' => 'FM',
                        'label' => 'Federated States of Micronesia',
                        'regions' =>
                            [
                                'State of Yap' => 'State of Yap',
                            ],
                    ],
                'FO' =>
                    [
                        'value' => 'FO',
                        'label' => 'Faroe Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'FR' =>
                    [
                        'value' => 'FR',
                        'label' => 'France',
                        'regions' =>
                            [
                                'Ain' => 'Ain',
                                'Aisne' => 'Aisne',
                                'Allier' => 'Allier',
                                'Ardennes' => 'Ardennes',
                                'Ardèche' => 'Ardèche',
                                'Ariège' => 'Ariège',
                                'Aube' => 'Aube',
                                'Aude' => 'Aude',
                                'Aveyron' => 'Aveyron',
                                'Bas-Rhin' => 'Bas-Rhin',
                                'Brittany' => 'Brittany',
                                'Calvados' => 'Calvados',
                                'Cantal' => 'Cantal',
                                'Centre' => 'Centre',
                                'Charente' => 'Charente',
                                'Charente-Maritime' => 'Charente-Maritime',
                                'Corrèze' => 'Corrèze',
                                'Corsica' => 'Corsica',
                                'Cote d\'Or' => 'Cote d\'Or',
                                'Creuse' => 'Creuse',
                                'Deux-Sèvres' => 'Deux-Sèvres',
                                'Dordogne' => 'Dordogne',
                                'Doubs' => 'Doubs',
                                'Drôme' => 'Drôme',
                                'Eure' => 'Eure',
                                'Gard' => 'Gard',
                                'Gers' => 'Gers',
                                'Gironde' => 'Gironde',
                                'Haut-Rhin' => 'Haut-Rhin',
                                'Haute-Loire' => 'Haute-Loire',
                                'Haute-Marne' => 'Haute-Marne',
                                'Haute-Savoie' => 'Haute-Savoie',
                                'Haute-Saône' => 'Haute-Saône',
                                'Haute-Vienne' => 'Haute-Vienne',
                                'Hautes-Pyrénées' => 'Hautes-Pyrénées',
                                'Hérault' => 'Hérault',
                                'Isère' => 'Isère',
                                'Jura' => 'Jura',
                                'Landes' => 'Landes',
                                'Loire' => 'Loire',
                                'Lot' => 'Lot',
                                'Lot-et-Garonne' => 'Lot-et-Garonne',
                                'Lozère' => 'Lozère',
                                'Manche' => 'Manche',
                                'Marne' => 'Marne',
                                'Meurthe et Moselle' => 'Meurthe et Moselle',
                                'Meuse' => 'Meuse',
                                'Moselle' => 'Moselle',
                                'Nièvre' => 'Nièvre',
                                'North' => 'North',
                                'Oise' => 'Oise',
                                'Orne' => 'Orne',
                                'Pas-de-Calais' => 'Pas-de-Calais',
                                'Pays de la Loire' => 'Pays de la Loire',
                                'Provence-Alpes-Côte d\'Azur' => 'Provence-Alpes-Côte d\'Azur',
                                'Puy-de-Dôme' => 'Puy-de-Dôme',
                                'Pyrénées-Atlantiques' => 'Pyrénées-Atlantiques',
                                'Pyrénées-Orientales' => 'Pyrénées-Orientales',
                                'Rhône' => 'Rhône',
                                'Savoy' => 'Savoy',
                                'Saône-et-Loire' => 'Saône-et-Loire',
                                'Seine-Maritime' => 'Seine-Maritime',
                                'Somme' => 'Somme',
                                'Tarn' => 'Tarn',
                                'Tarn-et-Garonne' => 'Tarn-et-Garonne',
                                'Territoire de Belfort' => 'Territoire de Belfort',
                                'Upper Garonne' => 'Upper Garonne',
                                'Vienne' => 'Vienne',
                                'Vosges' => 'Vosges',
                                'Yonne' => 'Yonne',
                                'Île-de-France' => 'Île-de-France',
                            ],
                    ],
                'GA' =>
                    [
                        'value' => 'GA',
                        'label' => 'Gabon',
                        'regions' =>
                            [
                                'Estuaire' => 'Estuaire',
                                'Haut-Ogooué' => 'Haut-Ogooué',
                                'Moyen-Ogooué' => 'Moyen-Ogooué',
                                'Ngouni' => 'Ngouni',
                                'Ogooué-Maritime' => 'Ogooué-Maritime',
                            ],
                    ],
                'GB' =>
                    [
                        'value' => 'GB',
                        'label' => 'United Kingdom',
                        'regions' =>
                            [
                                'England' => 'England',
                                'Northern Ireland' => 'Northern Ireland',
                                'Scotland' => 'Scotland',
                                'Wales' => 'Wales',
                            ],
                    ],
                'GD' =>
                    [
                        'value' => 'GD',
                        'label' => 'Grenada',
                        'regions' =>
                            [
                                'Saint Andrew' => 'Saint Andrew',
                                'Saint George' => 'Saint George',
                                'Saint Mark' => 'Saint Mark',
                                'Saint Patrick' => 'Saint Patrick',
                            ],
                    ],
                'GE' =>
                    [
                        'value' => 'GE',
                        'label' => 'Georgia',
                        'regions' =>
                            [
                                'Abkhazia' => 'Abkhazia',
                                'Ajaria' => 'Ajaria',
                                'Guria' => 'Guria',
                                'Imereti' => 'Imereti',
                                'K\'alak\'i T\'bilisi' => 'K\'alak\'i T\'bilisi',
                                'Kakheti' => 'Kakheti',
                                'Mtskheta-Mtianeti' => 'Mtskheta-Mtianeti',
                                'Racha-Lechkhumi and Kvemo Svaneti' => 'Racha-Lechkhumi and Kvemo Svaneti',
                                'Samegrelo and Zemo Svaneti' => 'Samegrelo and Zemo Svaneti',
                                'Shida Kartli' => 'Shida Kartli',
                            ],
                    ],
                'GF' =>
                    [
                        'value' => 'GF',
                        'label' => 'French Guiana',
                        'regions' =>
                            [
                            ],
                    ],
                'GG' =>
                    [
                        'value' => 'GG',
                        'label' => 'Guernsey',
                        'regions' =>
                            [
                            ],
                    ],
                'GH' =>
                    [
                        'value' => 'GH',
                        'label' => 'Ghana',
                        'regions' =>
                            [
                                'Ashanti Region' => 'Ashanti Region',
                                'Brong-Ahafo' => 'Brong-Ahafo',
                                'Central Region' => 'Central Region',
                                'Eastern Region' => 'Eastern Region',
                                'Greater Accra Region' => 'Greater Accra Region',
                                'Upper East Region' => 'Upper East Region',
                                'Upper West Region' => 'Upper West Region',
                                'Volta Region' => 'Volta Region',
                                'Western Region' => 'Western Region',
                            ],
                    ],
                'GI' =>
                    [
                        'value' => 'GI',
                        'label' => 'Gibraltar',
                        'regions' =>
                            [
                            ],
                    ],
                'GL' =>
                    [
                        'value' => 'GL',
                        'label' => 'Greenland',
                        'regions' =>
                            [
                                'Kujalleq' => 'Kujalleq',
                                'Qaasuitsup' => 'Qaasuitsup',
                                'Qeqqata' => 'Qeqqata',
                                'Sermersooq' => 'Sermersooq',
                            ],
                    ],
                'GM' =>
                    [
                        'value' => 'GM',
                        'label' => 'Gambia',
                        'regions' =>
                            [
                                'City of Banjul' => 'City of Banjul',
                                'Western Division' => 'Western Division',
                            ],
                    ],
                'GN' =>
                    [
                        'value' => 'GN',
                        'label' => 'Guinea',
                        'regions' =>
                            [
                                'Boke Region' => 'Boke Region',
                                'Conakry Region' => 'Conakry Region',
                                'Faranah' => 'Faranah',
                                'Kankan Region' => 'Kankan Region',
                                'Kindia' => 'Kindia',
                                'Labe Region' => 'Labe Region',
                                'Mamou Region' => 'Mamou Region',
                                'Nzerekore Region' => 'Nzerekore Region',
                            ],
                    ],
                'GP' =>
                    [
                        'value' => 'GP',
                        'label' => 'Guadeloupe',
                        'regions' =>
                            [
                            ],
                    ],
                'GQ' =>
                    [
                        'value' => 'GQ',
                        'label' => 'Equatorial Guinea',
                        'regions' =>
                            [
                                'Bioko Norte' => 'Bioko Norte',
                                'Wele-Nzas' => 'Wele-Nzas',
                            ],
                    ],
                'GR' =>
                    [
                        'value' => 'GR',
                        'label' => 'Greece',
                        'regions' =>
                            [
                                'Attica' => 'Attica',
                                'Central Greece' => 'Central Greece',
                                'Central Macedonia' => 'Central Macedonia',
                                'Crete' => 'Crete',
                                'East Macedonia and Thrace' => 'East Macedonia and Thrace',
                                'Epirus' => 'Epirus',
                                'Ionian Islands' => 'Ionian Islands',
                                'North Aegean' => 'North Aegean',
                                'Peloponnese' => 'Peloponnese',
                                'South Aegean' => 'South Aegean',
                                'Thessaly' => 'Thessaly',
                                'West Greece' => 'West Greece',
                                'West Macedonia' => 'West Macedonia',
                            ],
                    ],
                'GS' =>
                    [
                        'value' => 'GS',
                        'label' => 'South Georgia and the South Sandwich Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'GT' =>
                    [
                        'value' => 'GT',
                        'label' => 'Guatemala',
                        'regions' =>
                            [
                                'Departamento de Alta Verapaz' => 'Departamento de Alta Verapaz',
                                'Departamento de Chimaltenango' => 'Departamento de Chimaltenango',
                                'Departamento de Escuintla' => 'Departamento de Escuintla',
                                'Departamento de Guatemala' => 'Departamento de Guatemala',
                                'Departamento de Huehuetenango' => 'Departamento de Huehuetenango',
                                'Departamento de Jalapa' => 'Departamento de Jalapa',
                                'Departamento de Jutiapa' => 'Departamento de Jutiapa',
                                'Departamento de Quetzaltenango' => 'Departamento de Quetzaltenango',
                                'Departamento de Retalhuleu' => 'Departamento de Retalhuleu',
                                'Departamento de Sacatepequez' => 'Departamento de Sacatepequez',
                                'Departamento de Zacapa' => 'Departamento de Zacapa',
                                'Suchitepeque' => 'Suchitepeque',
                            ],
                    ],
                'GU' =>
                    [
                        'value' => 'GU',
                        'label' => 'Guam',
                        'regions' =>
                            [
                            ],
                    ],
                'GW' =>
                    [
                        'value' => 'GW',
                        'label' => 'Guinea-Bissau',
                        'regions' =>
                            [
                                'Bissau' => 'Bissau',
                                'Bolama and Bijagos' => 'Bolama and Bijagos',
                                'Cacheu Region' => 'Cacheu Region',
                            ],
                    ],
                'GY' =>
                    [
                        'value' => 'GY',
                        'label' => 'Guyana',
                        'regions' =>
                            [
                                'Demerara-Mahaica Region' => 'Demerara-Mahaica Region',
                                'East Berbice-Corentyne Region' => 'East Berbice-Corentyne Region',
                                'Upper Demerara-Berbice Region' => 'Upper Demerara-Berbice Region',
                            ],
                    ],
                'HK' =>
                    [
                        'value' => 'HK',
                        'label' => 'Hong Kong',
                        'regions' =>
                            [
                                'Central and Western District' => 'Central and Western District',
                                'Eastern' => 'Eastern',
                                'Kowloon City' => 'Kowloon City',
                                'North' => 'North',
                                'Sha Tin' => 'Sha Tin',
                                'Sham Shui Po' => 'Sham Shui Po',
                                'Southern' => 'Southern',
                                'Wanchai' => 'Wanchai',
                                'Wong Tai Sin' => 'Wong Tai Sin',
                                'Yau Tsim Mong' => 'Yau Tsim Mong',
                                'Yuen Long District' => 'Yuen Long District',
                            ],
                    ],
                'HN' =>
                    [
                        'value' => 'HN',
                        'label' => 'Honduras',
                        'regions' =>
                            [
                                'Bay Islands' => 'Bay Islands',
                                'Departamento de Atlantida' => 'Departamento de Atlantida',
                                'Departamento de Choluteca' => 'Departamento de Choluteca',
                                'Departamento de Colon' => 'Departamento de Colon',
                                'Departamento de Comayagua' => 'Departamento de Comayagua',
                                'Departamento de Copan' => 'Departamento de Copan',
                                'Departamento de Cortes' => 'Departamento de Cortes',
                                'Departamento de El Paraiso' => 'Departamento de El Paraiso',
                                'Departamento de Francisco Morazan' => 'Departamento de Francisco Morazan',
                                'Departamento de Gracias a Dios' => 'Departamento de Gracias a Dios',
                                'Departamento de La Paz' => 'Departamento de La Paz',
                                'Departamento de Lempira' => 'Departamento de Lempira',
                                'Departamento de Olancho' => 'Departamento de Olancho',
                                'Departamento de Santa Barbara' => 'Departamento de Santa Barbara',
                                'Departamento de Valle' => 'Departamento de Valle',
                                'Departamento de Yoro' => 'Departamento de Yoro',
                            ],
                    ],
                'HR' =>
                    [
                        'value' => 'HR',
                        'label' => 'Croatia',
                        'regions' =>
                            [
                                'Bjelovarsko-Bilogorska Zupanija' => 'Bjelovarsko-Bilogorska Zupanija',
                                'City of Zagreb' => 'City of Zagreb',
                                'Dubrovacko-Neretvanska Zupanija' => 'Dubrovacko-Neretvanska Zupanija',
                                'Istarska Zupanija' => 'Istarska Zupanija',
                                'Karlovacka Zupanija' => 'Karlovacka Zupanija',
                                'Koprivnicko-Krizevacka Zupanija' => 'Koprivnicko-Krizevacka Zupanija',
                                'Krapinsko-Zagorska Zupanija' => 'Krapinsko-Zagorska Zupanija',
                                'Licko-Senjska Zupanija' => 'Licko-Senjska Zupanija',
                                'Megimurska Zupanija' => 'Megimurska Zupanija',
                                'Osjecko-Baranjska Zupanija' => 'Osjecko-Baranjska Zupanija',
                                'Pozesko-Slavonska Zupanija' => 'Pozesko-Slavonska Zupanija',
                                'Primorsko-Goranska Zupanija' => 'Primorsko-Goranska Zupanija',
                                'Sibensko-Kninska Zupanija' => 'Sibensko-Kninska Zupanija',
                                'Sisacko-Moslavacka Zupanija' => 'Sisacko-Moslavacka Zupanija',
                                'Slavonski Brod-Posavina' => 'Slavonski Brod-Posavina',
                                'Splitsko-Dalmatinska Zupanija' => 'Splitsko-Dalmatinska Zupanija',
                                'Varazdinska Zupanija' => 'Varazdinska Zupanija',
                                'Viroviticko-Podravska Zupanija' => 'Viroviticko-Podravska Zupanija',
                                'Vukovar-Sirmium' => 'Vukovar-Sirmium',
                                'Zadarska Zupanija' => 'Zadarska Zupanija',
                                'Zagreb County' => 'Zagreb County',
                            ],
                    ],
                'HT' =>
                    [
                        'value' => 'HT',
                        'label' => 'Haiti',
                        'regions' =>
                            [
                                'Departement de l\'Ouest' => 'Departement de l\'Ouest',
                                'Nord' => 'Nord',
                                'Sud' => 'Sud',
                                'Sud-Est' => 'Sud-Est',
                            ],
                    ],
                'HU' =>
                    [
                        'value' => 'HU',
                        'label' => 'Hungary',
                        'regions' =>
                            [
                                'Baranya' => 'Baranya',
                                'Bekes' => 'Bekes',
                                'Borsod-Abaúj-Zemplén' => 'Borsod-Abaúj-Zemplén',
                                'Budapest' => 'Budapest',
                                'Bács-Kiskun' => 'Bács-Kiskun',
                                'Csongrad megye' => 'Csongrad megye',
                                'Fejér' => 'Fejér',
                                'Győr-Moson-Sopron' => 'Győr-Moson-Sopron',
                                'Hajdú-Bihar' => 'Hajdú-Bihar',
                                'Heves megye' => 'Heves megye',
                                'Jász-Nagykun-Szolnok' => 'Jász-Nagykun-Szolnok',
                                'Komárom-Esztergom' => 'Komárom-Esztergom',
                                'Nograd megye' => 'Nograd megye',
                                'Pest megye' => 'Pest megye',
                                'Somogy megye' => 'Somogy megye',
                                'Szabolcs-Szatmár-Bereg' => 'Szabolcs-Szatmár-Bereg',
                                'Tolna megye' => 'Tolna megye',
                                'Vas' => 'Vas',
                                'Veszprem megye' => 'Veszprem megye',
                                'Zala' => 'Zala',
                            ],
                    ],
                'ID' =>
                    [
                        'value' => 'ID',
                        'label' => 'Indonesia',
                        'regions' =>
                            [
                                'Aceh' => 'Aceh',
                                'Bali' => 'Bali',
                                'Bangka–Belitung Islands' => 'Bangka–Belitung Islands',
                                'Banten' => 'Banten',
                                'Bengkulu' => 'Bengkulu',
                                'Central Java' => 'Central Java',
                                'Central Kalimantan' => 'Central Kalimantan',
                                'Central Sulawesi' => 'Central Sulawesi',
                                'East Java' => 'East Java',
                                'East Kalimantan' => 'East Kalimantan',
                                'East Nusa Tenggara' => 'East Nusa Tenggara',
                                'Gorontalo' => 'Gorontalo',
                                'Jakarta' => 'Jakarta',
                                'Jambi' => 'Jambi',
                                'Lampung' => 'Lampung',
                                'Maluku' => 'Maluku',
                                'North Kalimantan' => 'North Kalimantan',
                                'North Maluku' => 'North Maluku',
                                'North Sulawesi' => 'North Sulawesi',
                                'North Sumatra' => 'North Sumatra',
                                'Papua' => 'Papua',
                                'Riau' => 'Riau',
                                'Riau Islands' => 'Riau Islands',
                                'South Kalimantan' => 'South Kalimantan',
                                'South Sulawesi' => 'South Sulawesi',
                                'South Sumatra' => 'South Sumatra',
                                'Southeast Sulawesi' => 'Southeast Sulawesi',
                                'West Java' => 'West Java',
                                'West Kalimantan' => 'West Kalimantan',
                                'West Nusa Tenggara' => 'West Nusa Tenggara',
                                'West Sulawesi' => 'West Sulawesi',
                                'West Sumatra' => 'West Sumatra',
                                'Yogyakarta' => 'Yogyakarta',
                            ],
                    ],
                'IE' =>
                    [
                        'value' => 'IE',
                        'label' => 'Ireland',
                        'regions' =>
                            [
                                'Connaught' => 'Connaught',
                                'Leinster' => 'Leinster',
                                'Munster' => 'Munster',
                                'Ulster' => 'Ulster',
                            ],
                    ],
                'IL' =>
                    [
                        'value' => 'IL',
                        'label' => 'Israel',
                        'regions' =>
                            [
                                'Central District' => 'Central District',
                                'Haifa' => 'Haifa',
                                'Jerusalem' => 'Jerusalem',
                                'Northern District' => 'Northern District',
                                'Southern District' => 'Southern District',
                                'Tel Aviv' => 'Tel Aviv',
                            ],
                    ],
                'IM' =>
                    [
                        'value' => 'IM',
                        'label' => 'Isle of Man',
                        'regions' =>
                            [
                            ],
                    ],
                'IN' =>
                    [
                        'value' => 'IN',
                        'label' => 'India',
                        'regions' =>
                            [
                                'Andhra Pradesh' => 'Andhra Pradesh',
                                'Arunachal Pradesh' => 'Arunachal Pradesh',
                                'Assam' => 'Assam',
                                'Bihar' => 'Bihar',
                                'Chandigarh' => 'Chandigarh',
                                'Chhattisgarh' => 'Chhattisgarh',
                                'Dadra and Nagar Haveli' => 'Dadra and Nagar Haveli',
                                'Daman and Diu' => 'Daman and Diu',
                                'Goa' => 'Goa',
                                'Gujarat' => 'Gujarat',
                                'Haryana' => 'Haryana',
                                'Himachal Pradesh' => 'Himachal Pradesh',
                                'Jharkhand' => 'Jharkhand',
                                'Karnataka' => 'Karnataka',
                                'Kashmir' => 'Kashmir',
                                'Kerala' => 'Kerala',
                                'Laccadives' => 'Laccadives',
                                'Madhya Pradesh' => 'Madhya Pradesh',
                                'Maharashtra' => 'Maharashtra',
                                'Manipur' => 'Manipur',
                                'Meghalaya' => 'Meghalaya',
                                'Mizoram' => 'Mizoram',
                                'Nagaland' => 'Nagaland',
                                'National Capital Territory of Delhi' => 'National Capital Territory of Delhi',
                                'Odisha' => 'Odisha',
                                'Punjab' => 'Punjab',
                                'Rajasthan' => 'Rajasthan',
                                'Sikkim' => 'Sikkim',
                                'Tamil Nadu' => 'Tamil Nadu',
                                'Telangana' => 'Telangana',
                                'Tripura' => 'Tripura',
                                'Union Territory of Andaman and Nicobar Islands' => 'Union Territory of Andaman and Nicobar Islands',
                                'Union Territory of Puducherry' => 'Union Territory of Puducherry',
                                'Uttar Pradesh' => 'Uttar Pradesh',
                                'Uttarakhand' => 'Uttarakhand',
                                'West Bengal' => 'West Bengal',
                            ],
                    ],
                'IO' =>
                    [
                        'value' => 'IO',
                        'label' => 'British Indian Ocean Territory',
                        'regions' =>
                            [
                            ],
                    ],
                'IQ' =>
                    [
                        'value' => 'IQ',
                        'label' => 'Iraq',
                        'regions' =>
                            [
                                'An Najaf' => 'An Najaf',
                                'Anbar' => 'Anbar',
                                'Basra Governorate' => 'Basra Governorate',
                                'Mayorality of Baghdad' => 'Mayorality of Baghdad',
                                'Maysan' => 'Maysan',
                                'Muhafazat Arbil' => 'Muhafazat Arbil',
                                'Muhafazat Babil' => 'Muhafazat Babil',
                                'Muhafazat Karbala\'' => 'Muhafazat Karbala\'',
                                'Muhafazat Kirkuk' => 'Muhafazat Kirkuk',
                                'Muhafazat Ninawa' => 'Muhafazat Ninawa',
                                'Muhafazat Wasit' => 'Muhafazat Wasit',
                                'Muhafazat as Sulaymaniyah' => 'Muhafazat as Sulaymaniyah',
                            ],
                    ],
                'IR' =>
                    [
                        'value' => 'IR',
                        'label' => 'Iran',
                        'regions' =>
                            [
                                'Alborz' => 'Alborz',
                                'Bushehr' => 'Bushehr',
                                'East Azerbaijan' => 'East Azerbaijan',
                                'Fars' => 'Fars',
                                'Hormozgan' => 'Hormozgan',
                                'Isfahan' => 'Isfahan',
                                'Kerman' => 'Kerman',
                                'Khuzestan' => 'Khuzestan',
                                'Markazi' => 'Markazi',
                                'Māzandarān' => 'Māzandarān',
                                'Ostan-e Ardabil' => 'Ostan-e Ardabil',
                                'Ostan-e Azarbayjan-e Gharbi' => 'Ostan-e Azarbayjan-e Gharbi',
                                'Ostan-e Chahar Mahal va Bakhtiari' => 'Ostan-e Chahar Mahal va Bakhtiari',
                                'Ostan-e Gilan' => 'Ostan-e Gilan',
                                'Ostan-e Golestan' => 'Ostan-e Golestan',
                                'Ostan-e Hamadan' => 'Ostan-e Hamadan',
                                'Ostan-e Ilam' => 'Ostan-e Ilam',
                                'Ostan-e Kermanshah' => 'Ostan-e Kermanshah',
                                'Ostan-e Khorasan-e Shomali' => 'Ostan-e Khorasan-e Shomali',
                                'Ostan-e Kordestan' => 'Ostan-e Kordestan',
                                'Ostan-e Qazvin' => 'Ostan-e Qazvin',
                                'Ostan-e Tehran' => 'Ostan-e Tehran',
                                'Razavi Khorasan' => 'Razavi Khorasan',
                                'Semnān' => 'Semnān',
                                'Sistan and Baluchestan' => 'Sistan and Baluchestan',
                                'Yazd' => 'Yazd',
                                'Zanjan' => 'Zanjan',
                            ],
                    ],
                'IS' =>
                    [
                        'value' => 'IS',
                        'label' => 'Iceland',
                        'regions' =>
                            [
                                'Capital Region' => 'Capital Region',
                                'East' => 'East',
                                'Northeast' => 'Northeast',
                                'Northwest' => 'Northwest',
                                'South' => 'South',
                                'Southern Peninsula' => 'Southern Peninsula',
                                'West' => 'West',
                                'Westfjords' => 'Westfjords',
                            ],
                    ],
                'IT' =>
                    [
                        'value' => 'IT',
                        'label' => 'Italy',
                        'regions' =>
                            [
                                'Abruzzo' => 'Abruzzo',
                                'Aosta Valley' => 'Aosta Valley',
                                'Apulia' => 'Apulia',
                                'Basilicate' => 'Basilicate',
                                'Calabria' => 'Calabria',
                                'Campania' => 'Campania',
                                'Emilia-Romagna' => 'Emilia-Romagna',
                                'Friuli Venezia Giulia' => 'Friuli Venezia Giulia',
                                'Latium' => 'Latium',
                                'Liguria' => 'Liguria',
                                'Lombardy' => 'Lombardy',
                                'Molise' => 'Molise',
                                'Piedmont' => 'Piedmont',
                                'Sardinia' => 'Sardinia',
                                'Sicily' => 'Sicily',
                                'The Marches' => 'The Marches',
                                'Trentino-Alto Adige' => 'Trentino-Alto Adige',
                                'Tuscany' => 'Tuscany',
                                'Umbria' => 'Umbria',
                                'Veneto' => 'Veneto',
                            ],
                    ],
                'JE' =>
                    [
                        'value' => 'JE',
                        'label' => 'Jersey',
                        'regions' =>
                            [
                            ],
                    ],
                'JM' =>
                    [
                        'value' => 'JM',
                        'label' => 'Jamaica',
                        'regions' =>
                            [
                                'Clarendon' => 'Clarendon',
                                'Kingston' => 'Kingston',
                                'Manchester' => 'Manchester',
                                'Parish of Saint Ann' => 'Parish of Saint Ann',
                                'Portland' => 'Portland',
                                'Saint Catherine' => 'Saint Catherine',
                                'Saint Elizabeth' => 'Saint Elizabeth',
                                'Saint James' => 'Saint James',
                                'Saint Mary' => 'Saint Mary',
                                'Saint Thomas' => 'Saint Thomas',
                                'Westmoreland' => 'Westmoreland',
                            ],
                    ],
                'JO' =>
                    [
                        'value' => 'JO',
                        'label' => 'Hashemite Kingdom of Jordan',
                        'regions' =>
                            [
                                'Ajloun' => 'Ajloun',
                                'Amman Governorate' => 'Amman Governorate',
                                'Aqaba' => 'Aqaba',
                                'Balqa' => 'Balqa',
                                'Irbid' => 'Irbid',
                                'Jerash' => 'Jerash',
                                'Karak' => 'Karak',
                                'Madaba' => 'Madaba',
                                'Mafraq' => 'Mafraq',
                                'Ma’an' => 'Ma’an',
                                'Tafielah' => 'Tafielah',
                                'Zarqa' => 'Zarqa',
                            ],
                    ],
                'JP' =>
                    [
                        'value' => 'JP',
                        'label' => 'Japan',
                        'regions' =>
                            [
                                'Aichi' => 'Aichi',
                                'Akita' => 'Akita',
                                'Aomori' => 'Aomori',
                                'Chiba' => 'Chiba',
                                'Ehime' => 'Ehime',
                                'Fukui' => 'Fukui',
                                'Fukuoka' => 'Fukuoka',
                                'Fukushima-ken' => 'Fukushima-ken',
                                'Gifu' => 'Gifu',
                                'Gunma' => 'Gunma',
                                'Hiroshima' => 'Hiroshima',
                                'Hokkaido' => 'Hokkaido',
                                'Hyōgo' => 'Hyōgo',
                                'Ibaraki' => 'Ibaraki',
                                'Ishikawa' => 'Ishikawa',
                                'Iwate' => 'Iwate',
                                'Kagawa' => 'Kagawa',
                                'Kagoshima' => 'Kagoshima',
                                'Kanagawa' => 'Kanagawa',
                                'Kochi' => 'Kochi',
                                'Kumamoto' => 'Kumamoto',
                                'Kyoto' => 'Kyoto',
                                'Mie' => 'Mie',
                                'Miyagi' => 'Miyagi',
                                'Miyazaki' => 'Miyazaki',
                                'Nagano' => 'Nagano',
                                'Nagasaki' => 'Nagasaki',
                                'Nara' => 'Nara',
                                'Niigata' => 'Niigata',
                                'Oita' => 'Oita',
                                'Okayama' => 'Okayama',
                                'Okinawa' => 'Okinawa',
                                'Saga Prefecture' => 'Saga Prefecture',
                                'Saitama' => 'Saitama',
                                'Shiga Prefecture' => 'Shiga Prefecture',
                                'Shimane' => 'Shimane',
                                'Shizuoka' => 'Shizuoka',
                                'Tochigi' => 'Tochigi',
                                'Tokushima' => 'Tokushima',
                                'Tokyo' => 'Tokyo',
                                'Tottori' => 'Tottori',
                                'Toyama' => 'Toyama',
                                'Wakayama' => 'Wakayama',
                                'Yamagata' => 'Yamagata',
                                'Yamaguchi' => 'Yamaguchi',
                                'Yamanashi' => 'Yamanashi',
                                'Ōsaka' => 'Ōsaka',
                            ],
                    ],
                'KE' =>
                    [
                        'value' => 'KE',
                        'label' => 'Kenya',
                        'regions' =>
                            [
                                'Bomet District' => 'Bomet District',
                                'Garissa District' => 'Garissa District',
                                'Homa Bay District' => 'Homa Bay District',
                                'Kericho District' => 'Kericho District',
                                'Kiambu District' => 'Kiambu District',
                                'Kilifi District' => 'Kilifi District',
                                'Kisii District' => 'Kisii District',
                                'Kisumu' => 'Kisumu',
                                'Kwale District' => 'Kwale District',
                                'Mandera District' => 'Mandera District',
                                'Mombasa District' => 'Mombasa District',
                                'Murang\'a District' => 'Murang\'a District',
                                'Nairobi Province' => 'Nairobi Province',
                                'Nakuru District' => 'Nakuru District',
                                'Nyeri District' => 'Nyeri District',
                                'Siaya District' => 'Siaya District',
                                'Tharaka District' => 'Tharaka District',
                                'Trans Nzoia District' => 'Trans Nzoia District',
                                'Uasin Gishu' => 'Uasin Gishu',
                            ],
                    ],
                'KG' =>
                    [
                        'value' => 'KG',
                        'label' => 'Kyrgyzstan',
                        'regions' =>
                            [
                                'Batken' => 'Batken',
                                'Chuyskaya Oblast\'' => 'Chuyskaya Oblast\'',
                                'Gorod Bishkek' => 'Gorod Bishkek',
                                'Issyk-Kul Region' => 'Issyk-Kul Region',
                                'Jalal-Abad oblast' => 'Jalal-Abad oblast',
                                'Naryn oblast' => 'Naryn oblast',
                                'Osh Oblasty' => 'Osh Oblasty',
                                'Talas' => 'Talas',
                            ],
                    ],
                'KH' =>
                    [
                        'value' => 'KH',
                        'label' => 'Cambodia',
                        'regions' =>
                            [
                                'Banteay Meanchey' => 'Banteay Meanchey',
                                'Battambang' => 'Battambang',
                                'Kandal' => 'Kandal',
                                'Phnom Penh' => 'Phnom Penh',
                                'Preah Sihanouk' => 'Preah Sihanouk',
                            ],
                    ],
                'KI' =>
                    [
                        'value' => 'KI',
                        'label' => 'Kiribati',
                        'regions' =>
                            [
                                'Gilbert Islands' => 'Gilbert Islands',
                            ],
                    ],
                'KM' =>
                    [
                        'value' => 'KM',
                        'label' => 'Comoros',
                        'regions' =>
                            [
                                'Grande Comore' => 'Grande Comore',
                                'Ndzuwani' => 'Ndzuwani',
                            ],
                    ],
                'KN' =>
                    [
                        'value' => 'KN',
                        'label' => 'Saint Kitts and Nevis',
                        'regions' =>
                            [
                                'Saint George Basseterre' => 'Saint George Basseterre',
                                'Saint Mary Cayon' => 'Saint Mary Cayon',
                                'Saint Paul Charlestown' => 'Saint Paul Charlestown',
                            ],
                    ],
                'KP' =>
                    [
                        'value' => 'KP',
                        'label' => 'North Korea',
                        'regions' =>
                            [
                                'Chagang-do' => 'Chagang-do',
                                'Pyongyang' => 'Pyongyang',
                            ],
                    ],
                'KR' =>
                    [
                        'value' => 'KR',
                        'label' => 'Republic of Korea',
                        'regions' =>
                            [
                                'Busan' => 'Busan',
                                'Chungcheongbuk-do' => 'Chungcheongbuk-do',
                                'Chungcheongnam-do' => 'Chungcheongnam-do',
                                'Daegu' => 'Daegu',
                                'Daejeon' => 'Daejeon',
                                'Gangwon-do' => 'Gangwon-do',
                                'Gwangju' => 'Gwangju',
                                'Gyeonggi-do' => 'Gyeonggi-do',
                                'Gyeongsangbuk-do' => 'Gyeongsangbuk-do',
                                'Gyeongsangnam-do' => 'Gyeongsangnam-do',
                                'Incheon' => 'Incheon',
                                'Jeju-do' => 'Jeju-do',
                                'Jeollabuk-do' => 'Jeollabuk-do',
                                'Jeollanam-do' => 'Jeollanam-do',
                                'Seoul' => 'Seoul',
                                'Ulsan' => 'Ulsan',
                            ],
                    ],
                'KW' =>
                    [
                        'value' => 'KW',
                        'label' => 'Kuwait',
                        'regions' =>
                            [
                                'Al Asimah' => 'Al Asimah',
                                'Al Aḩmadī' => 'Al Aḩmadī',
                                'Al Farwaniyah' => 'Al Farwaniyah',
                                'Hawalli' => 'Hawalli',
                            ],
                    ],
                'KY' =>
                    [
                        'value' => 'KY',
                        'label' => 'Cayman Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'KZ' =>
                    [
                        'value' => 'KZ',
                        'label' => 'Kazakhstan',
                        'regions' =>
                            [
                                'Aktyubinskaya Oblast\'' => 'Aktyubinskaya Oblast\'',
                                'Almaty Oblysy' => 'Almaty Oblysy',
                                'Almaty Qalasy' => 'Almaty Qalasy',
                                'Aqmola Oblysy' => 'Aqmola Oblysy',
                                'Astana Qalasy' => 'Astana Qalasy',
                                'Atyrau Oblysy' => 'Atyrau Oblysy',
                                'East Kazakhstan' => 'East Kazakhstan',
                                'Mangistauskaya Oblast\'' => 'Mangistauskaya Oblast\'',
                                'Pavlodar Oblysy' => 'Pavlodar Oblysy',
                                'Qaraghandy Oblysy' => 'Qaraghandy Oblysy',
                                'Qostanay Oblysy' => 'Qostanay Oblysy',
                                'Qyzylorda Oblysy' => 'Qyzylorda Oblysy',
                                'Severo-Kazakhstanskaya Oblast\'' => 'Severo-Kazakhstanskaya Oblast\'',
                                'Yuzhno-Kazakhstanskaya Oblast\'' => 'Yuzhno-Kazakhstanskaya Oblast\'',
                                'Zapadno-Kazakhstanskaya Oblast\'' => 'Zapadno-Kazakhstanskaya Oblast\'',
                                'Zhambyl Oblysy' => 'Zhambyl Oblysy',
                            ],
                    ],
                'LA' =>
                    [
                        'value' => 'LA',
                        'label' => 'Laos',
                        'regions' =>
                            [
                                'Khammouan' => 'Khammouan',
                                'Vientiane' => 'Vientiane',
                            ],
                    ],
                'LB' =>
                    [
                        'value' => 'LB',
                        'label' => 'Lebanon',
                        'regions' =>
                            [
                                'Beyrouth' => 'Beyrouth',
                                'Mohafazat Aakkar' => 'Mohafazat Aakkar',
                                'Mohafazat Baalbek-Hermel' => 'Mohafazat Baalbek-Hermel',
                                'Mohafazat Liban-Nord' => 'Mohafazat Liban-Nord',
                                'Mohafazat Mont-Liban' => 'Mohafazat Mont-Liban',
                                'South Governorate' => 'South Governorate',
                            ],
                    ],
                'LC' =>
                    [
                        'value' => 'LC',
                        'label' => 'Saint Lucia',
                        'regions' =>
                            [
                                'Anse-la-Raye' => 'Anse-la-Raye',
                                'Castries Quarter' => 'Castries Quarter',
                                'Choiseul Quarter' => 'Choiseul Quarter',
                                'Dennery Quarter' => 'Dennery Quarter',
                                'Gros-Islet' => 'Gros-Islet',
                                'Laborie Quarter' => 'Laborie Quarter',
                                'Micoud Quarter' => 'Micoud Quarter',
                                'Quarter of Dauphin' => 'Quarter of Dauphin',
                                'Quarter of Praslin' => 'Quarter of Praslin',
                                'Soufriere' => 'Soufriere',
                                'Vieux-Fort' => 'Vieux-Fort',
                            ],
                    ],
                'LI' =>
                    [
                        'value' => 'LI',
                        'label' => 'Liechtenstein',
                        'regions' =>
                            [
                                'Balzers' => 'Balzers',
                                'Eschen' => 'Eschen',
                                'Gemeinde Gamprin' => 'Gemeinde Gamprin',
                                'Mauren' => 'Mauren',
                                'Ruggell' => 'Ruggell',
                                'Schaan' => 'Schaan',
                                'Schellenberg' => 'Schellenberg',
                                'Triesen' => 'Triesen',
                                'Triesenberg' => 'Triesenberg',
                                'Vaduz' => 'Vaduz',
                            ],
                    ],
                'LK' =>
                    [
                        'value' => 'LK',
                        'label' => 'Sri Lanka',
                        'regions' =>
                            [
                                'Central Province' => 'Central Province',
                                'Eastern Province' => 'Eastern Province',
                                'North Central Province' => 'North Central Province',
                                'North Western Province' => 'North Western Province',
                                'Province of Sabaragamuwa' => 'Province of Sabaragamuwa',
                                'Province of Uva' => 'Province of Uva',
                                'Southern Province' => 'Southern Province',
                                'Western Province' => 'Western Province',
                            ],
                    ],
                'LR' =>
                    [
                        'value' => 'LR',
                        'label' => 'Liberia',
                        'regions' =>
                            [
                                'Montserrado County' => 'Montserrado County',
                                'Nimba County' => 'Nimba County',
                            ],
                    ],
                'LS' =>
                    [
                        'value' => 'LS',
                        'label' => 'Lesotho',
                        'regions' =>
                            [
                                'Berea' => 'Berea',
                                'Leribe' => 'Leribe',
                                'Maseru' => 'Maseru',
                            ],
                    ],
                'LT' =>
                    [
                        'value' => 'LT',
                        'label' => 'Republic of Lithuania',
                        'regions' =>
                            [
                                'Alytus County' => 'Alytus County',
                                'Kaunas County' => 'Kaunas County',
                                'Klaipėda County' => 'Klaipėda County',
                                'Marijampolė County' => 'Marijampolė County',
                                'Panevėžys' => 'Panevėžys',
                                'Tauragė County' => 'Tauragė County',
                                'Telšiai County' => 'Telšiai County',
                                'Utena County' => 'Utena County',
                                'Vilnius County' => 'Vilnius County',
                                'Šiauliai County' => 'Šiauliai County',
                            ],
                    ],
                'LU' =>
                    [
                        'value' => 'LU',
                        'label' => 'Luxembourg',
                        'regions' =>
                            [
                                'District de Diekirch' => 'District de Diekirch',
                                'District de Grevenmacher' => 'District de Grevenmacher',
                                'District de Luxembourg' => 'District de Luxembourg',
                            ],
                    ],
                'LV' =>
                    [
                        'value' => 'LV',
                        'label' => 'Latvia',
                        'regions' =>
                            [
                                'Aizkraukles Rajons' => 'Aizkraukles Rajons',
                                'Aizpute' => 'Aizpute',
                                'Aloja' => 'Aloja',
                                'Babīte' => 'Babīte',
                                'Baldone' => 'Baldone',
                                'Balvu Novads' => 'Balvu Novads',
                                'Bauskas Novads' => 'Bauskas Novads',
                                'Brocēni' => 'Brocēni',
                                'Burtnieki' => 'Burtnieki',
                                'Carnikava' => 'Carnikava',
                                'Cesu Novads' => 'Cesu Novads',
                                'Daugavpils' => 'Daugavpils',
                                'Daugavpils municipality' => 'Daugavpils municipality',
                                'Dobeles Rajons' => 'Dobeles Rajons',
                                'Dundaga' => 'Dundaga',
                                'Engure' => 'Engure',
                                'Garkalne' => 'Garkalne',
                                'Grobiņa' => 'Grobiņa',
                                'Gulbenes Rajons' => 'Gulbenes Rajons',
                                'Ikšķile' => 'Ikšķile',
                                'Inčukalns' => 'Inčukalns',
                                'Jaunpiebalga' => 'Jaunpiebalga',
                                'Jelgava' => 'Jelgava',
                                'Jelgavas Rajons' => 'Jelgavas Rajons',
                                'Jurmala' => 'Jurmala',
                                'Jēkabpils Municipality' => 'Jēkabpils Municipality',
                                'Kandava' => 'Kandava',
                                'Koknese' => 'Koknese',
                                'Kuldigas Rajons' => 'Kuldigas Rajons',
                                'Lecava' => 'Lecava',
                                'Lielvārde' => 'Lielvārde',
                                'Liepaja' => 'Liepaja',
                                'Limbazu Rajons' => 'Limbazu Rajons',
                                'Ludzas Rajons' => 'Ludzas Rajons',
                                'Līvāni' => 'Līvāni',
                                'Madona Municipality' => 'Madona Municipality',
                                'Mārupe' => 'Mārupe',
                                'Ogre' => 'Ogre',
                                'Olaine' => 'Olaine',
                                'Ozolnieku Novads' => 'Ozolnieku Novads',
                                'Plavinu Novads' => 'Plavinu Novads',
                                'Preili Municipality' => 'Preili Municipality',
                                'Rezekne' => 'Rezekne',
                                'Riga' => 'Riga',
                                'Rojas Novads' => 'Rojas Novads',
                                'Ropazu Novads' => 'Ropazu Novads',
                                'Rugaju Novads' => 'Rugaju Novads',
                                'Rujienas Novads' => 'Rujienas Novads',
                                'Salacgrivas Novads' => 'Salacgrivas Novads',
                                'Salaspils Novads' => 'Salaspils Novads',
                                'Saldus Municipality' => 'Saldus Municipality',
                                'Saulkrastu Novads' => 'Saulkrastu Novads',
                                'Siguldas Novads' => 'Siguldas Novads',
                                'Skrundas Novads' => 'Skrundas Novads',
                                'Stopinu Novads' => 'Stopinu Novads',
                                'Talsi Municipality' => 'Talsi Municipality',
                                'Tukuma Rajons' => 'Tukuma Rajons',
                                'Vainodes Novads' => 'Vainodes Novads',
                                'Valka Municipality' => 'Valka Municipality',
                                'Valmiera District' => 'Valmiera District',
                                'Varaklanu Novads' => 'Varaklanu Novads',
                                'Ventspils' => 'Ventspils',
                                'Viesites Novads' => 'Viesites Novads',
                                'Zilupes Novads' => 'Zilupes Novads',
                                'Ādaži' => 'Ādaži',
                                'Ķegums' => 'Ķegums',
                                'Ķekava' => 'Ķekava',
                            ],
                    ],
                'LY' =>
                    [
                        'value' => 'LY',
                        'label' => 'Libya',
                        'regions' =>
                            [
                                'Sha\'biyat Banghazi' => 'Sha\'biyat Banghazi',
                                'Sha\'biyat Misratah' => 'Sha\'biyat Misratah',
                                'Sha\'biyat Sabha' => 'Sha\'biyat Sabha',
                                'Sha`biyat Nalut' => 'Sha`biyat Nalut',
                                'Tripoli' => 'Tripoli',
                            ],
                    ],
                'MA' =>
                    [
                        'value' => 'MA',
                        'label' => 'Morocco',
                        'regions' =>
                            [
                                'Chaouia-Ouardigha' => 'Chaouia-Ouardigha',
                                'Doukkala-Abda' => 'Doukkala-Abda',
                                'Gharb-Chrarda-Beni Hssen' => 'Gharb-Chrarda-Beni Hssen',
                                'Guelmim-Es Semara' => 'Guelmim-Es Semara',
                                'Laayoune-Boujdour-Sakia El Hamra' => 'Laayoune-Boujdour-Sakia El Hamra',
                                'Marrakech-Tensift-Al Haouz' => 'Marrakech-Tensift-Al Haouz',
                                'Oriental' => 'Oriental',
                                'Oued-Ed-Dahab' => 'Oued-Ed-Dahab',
                                'Region de Fes-Boulemane' => 'Region de Fes-Boulemane',
                                'Region de Meknes-Tafilalet' => 'Region de Meknes-Tafilalet',
                                'Region de Rabat-Sale-Zemmour-Zaer' => 'Region de Rabat-Sale-Zemmour-Zaer',
                                'Region de Souss-Massa-Draa' => 'Region de Souss-Massa-Draa',
                                'Region de Tanger-Tetouan' => 'Region de Tanger-Tetouan',
                                'Region du Grand Casablanca' => 'Region du Grand Casablanca',
                                'Tadla-Azilal' => 'Tadla-Azilal',
                                'Taza-Al Hoceima-Taounate' => 'Taza-Al Hoceima-Taounate',
                            ],
                    ],
                'MC' =>
                    [
                        'value' => 'MC',
                        'label' => 'Monaco',
                        'regions' =>
                            [
                            ],
                    ],
                'MD' =>
                    [
                        'value' => 'MD',
                        'label' => 'Republic of Moldova',
                        'regions' =>
                            [
                                'Anenii Noi' => 'Anenii Noi',
                                'Basarabeasca' => 'Basarabeasca',
                                'Briceni' => 'Briceni',
                                'Cahul' => 'Cahul',
                                'Cantemir' => 'Cantemir',
                                'Cimişlia' => 'Cimişlia',
                                'Criuleni' => 'Criuleni',
                                'Donduşeni' => 'Donduşeni',
                                'Drochia' => 'Drochia',
                                'Floreşti' => 'Floreşti',
                                'Făleşti' => 'Făleşti',
                                'Gagauzia' => 'Gagauzia',
                                'Glodeni' => 'Glodeni',
                                'Hînceşti' => 'Hînceşti',
                                'Laloveni' => 'Laloveni',
                                'Leova' => 'Leova',
                                'Municipiul Balti' => 'Municipiul Balti',
                                'Municipiul Bender' => 'Municipiul Bender',
                                'Municipiul Chisinau' => 'Municipiul Chisinau',
                                'Nisporeni' => 'Nisporeni',
                                'Orhei' => 'Orhei',
                                'Raionul Causeni' => 'Raionul Causeni',
                                'Raionul Dubasari' => 'Raionul Dubasari',
                                'Raionul Edineţ' => 'Raionul Edineţ',
                                'Raionul Ocniţa' => 'Raionul Ocniţa',
                                'Raionul Soroca' => 'Raionul Soroca',
                                'Raionul Stefan Voda' => 'Raionul Stefan Voda',
                                'Rezina' => 'Rezina',
                                'Rîşcani' => 'Rîşcani',
                                'Strășeni' => 'Strășeni',
                                'Sîngerei' => 'Sîngerei',
                                'Taraclia' => 'Taraclia',
                                'Teleneşti' => 'Teleneşti',
                                'Ungheni' => 'Ungheni',
                                'Unitatea Teritoriala din Stinga Nistrului' => 'Unitatea Teritoriala din Stinga Nistrului',
                                'Şoldăneşti' => 'Şoldăneşti',
                            ],
                    ],
                'ME' =>
                    [
                        'value' => 'ME',
                        'label' => 'Montenegro',
                        'regions' =>
                            [
                                'Berane' => 'Berane',
                                'Budva' => 'Budva',
                                'Danilovgrad' => 'Danilovgrad',
                                'Herceg Novi' => 'Herceg Novi',
                                'Kotor' => 'Kotor',
                                'Podgorica' => 'Podgorica',
                                'Ulcinj' => 'Ulcinj',
                            ],
                    ],
                'MF' =>
                    [
                        'value' => 'MF',
                        'label' => 'Saint Martin',
                        'regions' =>
                            [
                            ],
                    ],
                'MG' =>
                    [
                        'value' => 'MG',
                        'label' => 'Madagascar',
                        'regions' =>
                            [
                            ],
                    ],
                'MH' =>
                    [
                        'value' => 'MH',
                        'label' => 'Marshall Islands',
                        'regions' =>
                            [
                                'Majuro Atoll' => 'Majuro Atoll',
                            ],
                    ],
                'MK' =>
                    [
                        'value' => 'MK',
                        'label' => 'Macedonia',
                        'regions' =>
                            [
                                'Bitola' => 'Bitola',
                                'Bogdanci' => 'Bogdanci',
                                'Bogovinje' => 'Bogovinje',
                                'Debar' => 'Debar',
                                'Demir Hisar' => 'Demir Hisar',
                                'Gevgelija' => 'Gevgelija',
                                'Gostivar' => 'Gostivar',
                                'Gradsko' => 'Gradsko',
                                'Kavadarci' => 'Kavadarci',
                                'Kisela Voda' => 'Kisela Voda',
                                'Kratovo' => 'Kratovo',
                                'Kumanovo' => 'Kumanovo',
                                'Makedonski Brod' => 'Makedonski Brod',
                                'Negotino' => 'Negotino',
                                'Novo Selo' => 'Novo Selo',
                                'Ohrid' => 'Ohrid',
                                'Opstina Karpos' => 'Opstina Karpos',
                                'Opstina Kicevo' => 'Opstina Kicevo',
                                'Opstina Kocani' => 'Opstina Kocani',
                                'Opstina Lipkovo' => 'Opstina Lipkovo',
                                'Opstina Probistip' => 'Opstina Probistip',
                                'Opstina Radovis' => 'Opstina Radovis',
                                'Opstina Stip' => 'Opstina Stip',
                                'Opstina Vrapciste' => 'Opstina Vrapciste',
                                'Prilep' => 'Prilep',
                                'Resen Municipality' => 'Resen Municipality',
                                'Struga' => 'Struga',
                                'Strumica' => 'Strumica',
                                'Tetovo' => 'Tetovo',
                                'Valandovo Municipality' => 'Valandovo Municipality',
                                'Veles' => 'Veles',
                            ],
                    ],
                'ML' =>
                    [
                        'value' => 'ML',
                        'label' => 'Mali',
                        'regions' =>
                            [
                                'Bamako Region' => 'Bamako Region',
                            ],
                    ],
                'MM' =>
                    [
                        'value' => 'MM',
                        'label' => 'Myanmar [Burma]',
                        'regions' =>
                            [
                                'Kayah State' => 'Kayah State',
                                'Magway Region' => 'Magway Region',
                                'Mandalay Region' => 'Mandalay Region',
                                'Yangon Region' => 'Yangon Region',
                            ],
                    ],
                'MN' =>
                    [
                        'value' => 'MN',
                        'label' => 'Mongolia',
                        'regions' =>
                            [
                                'Arhangay Aymag' => 'Arhangay Aymag',
                                'Bayan-OElgiy Aymag' => 'Bayan-OElgiy Aymag',
                                'Bayanhongor Aymag' => 'Bayanhongor Aymag',
                                'Central Aimak' => 'Central Aimak',
                                'East Gobi Aymag' => 'East Gobi Aymag',
                                'Govi-Altay Aymag' => 'Govi-Altay Aymag',
                                'Govi-Sumber' => 'Govi-Sumber',
                                'Hentiy Aymag' => 'Hentiy Aymag',
                                'Hovd' => 'Hovd',
                                'Hovsgol Aymag' => 'Hovsgol Aymag',
                                'Middle Govĭ' => 'Middle Govĭ',
                                'Selenge Aymag' => 'Selenge Aymag',
                                'Suhbaatar Aymag' => 'Suhbaatar Aymag',
                                'Ulaanbaatar Hot' => 'Ulaanbaatar Hot',
                                'Ömnögovĭ' => 'Ömnögovĭ',
                                'Övörhangay' => 'Övörhangay',
                            ],
                    ],
                'MO' =>
                    [
                        'value' => 'MO',
                        'label' => 'Macao',
                        'regions' =>
                            [
                            ],
                    ],
                'MP' =>
                    [
                        'value' => 'MP',
                        'label' => 'Northern Mariana Islands',
                        'regions' =>
                            [
                                'Saipan' => 'Saipan',
                            ],
                    ],
                'MQ' =>
                    [
                        'value' => 'MQ',
                        'label' => 'Martinique',
                        'regions' =>
                            [
                            ],
                    ],
                'MR' =>
                    [
                        'value' => 'MR',
                        'label' => 'Mauritania',
                        'regions' =>
                            [
                                'District de Nouakchott' => 'District de Nouakchott',
                            ],
                    ],
                'MS' =>
                    [
                        'value' => 'MS',
                        'label' => 'Montserrat',
                        'regions' =>
                            [
                            ],
                    ],
                'MT' =>
                    [
                        'value' => 'MT',
                        'label' => 'Malta',
                        'regions' =>
                            [
                                'Attard' => 'Attard',
                                'Balzan' => 'Balzan',
                                'Birkirkara' => 'Birkirkara',
                                'Birzebbuga' => 'Birzebbuga',
                                'Bormla' => 'Bormla',
                                'Ghajnsielem' => 'Ghajnsielem',
                                'Hal Gharghur' => 'Hal Gharghur',
                                'Hal Ghaxaq' => 'Hal Ghaxaq',
                                'Haz-Zabbar' => 'Haz-Zabbar',
                                'Haz-Zebbug' => 'Haz-Zebbug',
                                'Il-Belt Valletta' => 'Il-Belt Valletta',
                                'Il-Birgu' => 'Il-Birgu',
                                'Il-Fgura' => 'Il-Fgura',
                                'Il-Furjana' => 'Il-Furjana',
                                'Il-Gudja' => 'Il-Gudja',
                                'Il-Gzira' => 'Il-Gzira',
                                'Il-Hamrun' => 'Il-Hamrun',
                                'Il-Kalkara' => 'Il-Kalkara',
                                'Il-Marsa' => 'Il-Marsa',
                                'Il-Mellieha' => 'Il-Mellieha',
                                'Il-Mosta' => 'Il-Mosta',
                                'Il-Munxar' => 'Il-Munxar',
                                'Il-Qala' => 'Il-Qala',
                                'Il-Qrendi' => 'Il-Qrendi',
                                'In-Naxxar' => 'In-Naxxar',
                                'Ir-Rabat' => 'Ir-Rabat',
                                'Is-Siggiewi' => 'Is-Siggiewi',
                                'Is-Swieqi' => 'Is-Swieqi',
                                'Ix-Xaghra' => 'Ix-Xaghra',
                                'Ix-Xewkija' => 'Ix-Xewkija',
                                'Iz-Zebbug' => 'Iz-Zebbug',
                                'Iz-Zejtun' => 'Iz-Zejtun',
                                'Iz-Zurrieq' => 'Iz-Zurrieq',
                                'Kirkop' => 'Kirkop',
                                'L-Gharb' => 'L-Gharb',
                                'L-Ghasri' => 'L-Ghasri',
                                'L-Iklin' => 'L-Iklin',
                                'L-Imdina' => 'L-Imdina',
                                'L-Imgarr' => 'L-Imgarr',
                                'L-Imqabba' => 'L-Imqabba',
                                'L-Imsida' => 'L-Imsida',
                                'L-Imtarfa' => 'L-Imtarfa',
                                'L-Isla' => 'L-Isla',
                                'Lija' => 'Lija',
                                'Luqa' => 'Luqa',
                                'Marsaskala' => 'Marsaskala',
                                'Marsaxlokk' => 'Marsaxlokk',
                                'Paola' => 'Paola',
                                'Qormi' => 'Qormi',
                                'Safi' => 'Safi',
                                'Saint John' => 'Saint John',
                                'Saint Julian' => 'Saint Julian',
                                'Saint Lawrence' => 'Saint Lawrence',
                                'Saint Lucia' => 'Saint Lucia',
                                'Saint Paul’s Bay' => 'Saint Paul’s Bay',
                                'Saint Venera' => 'Saint Venera',
                                'Sannat' => 'Sannat',
                                'Ta\' Xbiex' => 'Ta\' Xbiex',
                                'Tal-Pieta' => 'Tal-Pieta',
                                'Tarxien' => 'Tarxien',
                                'Tas-Sliema' => 'Tas-Sliema',
                                'Victoria' => 'Victoria',
                            ],
                    ],
                'MU' =>
                    [
                        'value' => 'MU',
                        'label' => 'Mauritius',
                        'regions' =>
                            [
                                'Black River District' => 'Black River District',
                                'Flacq District' => 'Flacq District',
                                'Moka District' => 'Moka District',
                                'Pamplemousses District' => 'Pamplemousses District',
                                'Plaines Wilhems District' => 'Plaines Wilhems District',
                                'Port Louis District' => 'Port Louis District',
                                'Riviere du Rempart District' => 'Riviere du Rempart District',
                                'Rodrigues' => 'Rodrigues',
                                'Savanne District' => 'Savanne District',
                            ],
                    ],
                'MV' =>
                    [
                        'value' => 'MV',
                        'label' => 'Maldives',
                        'regions' =>
                            [
                                'Kaafu Atoll' => 'Kaafu Atoll',
                            ],
                    ],
                'MW' =>
                    [
                        'value' => 'MW',
                        'label' => 'Malawi',
                        'regions' =>
                            [
                                'Central Region' => 'Central Region',
                                'Northern Region' => 'Northern Region',
                                'Southern Region' => 'Southern Region',
                            ],
                    ],
                'MX' =>
                    [
                        'value' => 'MX',
                        'label' => 'Mexico',
                        'regions' =>
                            [
                                'Aguascalientes' => 'Aguascalientes',
                                'Baja California Sur' => 'Baja California Sur',
                                'Campeche' => 'Campeche',
                                'Chiapas' => 'Chiapas',
                                'Chihuahua' => 'Chihuahua',
                                'Coahuila' => 'Coahuila',
                                'Colima' => 'Colima',
                                'Durango' => 'Durango',
                                'Estado de Baja California' => 'Estado de Baja California',
                                'Estado de Mexico' => 'Estado de Mexico',
                                'Guanajuato' => 'Guanajuato',
                                'Guerrero' => 'Guerrero',
                                'Hidalgo' => 'Hidalgo',
                                'Jalisco' => 'Jalisco',
                                'Mexico City' => 'Mexico City',
                                'Michoacán' => 'Michoacán',
                                'Morelos' => 'Morelos',
                                'Nayarit' => 'Nayarit',
                                'Nuevo León' => 'Nuevo León',
                                'Oaxaca' => 'Oaxaca',
                                'Puebla' => 'Puebla',
                                'Querétaro' => 'Querétaro',
                                'Quintana Roo' => 'Quintana Roo',
                                'San Luis Potosí' => 'San Luis Potosí',
                                'Sinaloa' => 'Sinaloa',
                                'Sonora' => 'Sonora',
                                'Tabasco' => 'Tabasco',
                                'Tamaulipas' => 'Tamaulipas',
                                'Tlaxcala' => 'Tlaxcala',
                                'Veracruz' => 'Veracruz',
                                'Yucatán' => 'Yucatán',
                                'Zacatecas' => 'Zacatecas',
                            ],
                    ],
                'MY' =>
                    [
                        'value' => 'MY',
                        'label' => 'Malaysia',
                        'regions' =>
                            [
                                'Johor' => 'Johor',
                                'Kedah' => 'Kedah',
                                'Kelantan' => 'Kelantan',
                                'Kuala Lumpur' => 'Kuala Lumpur',
                                'Labuan' => 'Labuan',
                                'Melaka' => 'Melaka',
                                'Negeri Sembilan' => 'Negeri Sembilan',
                                'Pahang' => 'Pahang',
                                'Penang' => 'Penang',
                                'Perak' => 'Perak',
                                'Perlis' => 'Perlis',
                                'Putrajaya' => 'Putrajaya',
                                'Sabah' => 'Sabah',
                                'Sarawak' => 'Sarawak',
                                'Selangor' => 'Selangor',
                                'Terengganu' => 'Terengganu',
                            ],
                    ],
                'MZ' =>
                    [
                        'value' => 'MZ',
                        'label' => 'Mozambique',
                        'regions' =>
                            [
                                'Cabo Delgado Province' => 'Cabo Delgado Province',
                                'Cidade de Maputo' => 'Cidade de Maputo',
                                'Gaza Province' => 'Gaza Province',
                                'Inhambane Province' => 'Inhambane Province',
                                'Manica Province' => 'Manica Province',
                                'Maputo Province' => 'Maputo Province',
                                'Nampula' => 'Nampula',
                                'Niassa Province' => 'Niassa Province',
                                'Provincia de Zambezia' => 'Provincia de Zambezia',
                                'Sofala Province' => 'Sofala Province',
                                'Tete' => 'Tete',
                            ],
                    ],
                'NA' =>
                    [
                        'value' => 'NA',
                        'label' => 'Namibia',
                        'regions' =>
                            [
                                'Erongo' => 'Erongo',
                                'Karas' => 'Karas',
                                'Kavango East' => 'Kavango East',
                                'Khomas' => 'Khomas',
                                'Kunene' => 'Kunene',
                                'Omaheke' => 'Omaheke',
                                'Omusati' => 'Omusati',
                                'Oshana' => 'Oshana',
                                'Oshikoto' => 'Oshikoto',
                                'Otjozondjupa' => 'Otjozondjupa',
                                'Zambezi Region' => 'Zambezi Region',
                            ],
                    ],
                'NC' =>
                    [
                        'value' => 'NC',
                        'label' => 'New Caledonia',
                        'regions' =>
                            [
                                'South Province' => 'South Province',
                            ],
                    ],
                'NE' =>
                    [
                        'value' => 'NE',
                        'label' => 'Niger',
                        'regions' =>
                            [
                                'Niamey' => 'Niamey',
                            ],
                    ],
                'NF' =>
                    [
                        'value' => 'NF',
                        'label' => 'Norfolk Island',
                        'regions' =>
                            [
                            ],
                    ],
                'NG' =>
                    [
                        'value' => 'NG',
                        'label' => 'Nigeria',
                        'regions' =>
                            [
                                'Abia State' => 'Abia State',
                                'Adamawa' => 'Adamawa',
                                'Akwa Ibom State' => 'Akwa Ibom State',
                                'Anambra' => 'Anambra',
                                'Bauchi' => 'Bauchi',
                                'Bayelsa State' => 'Bayelsa State',
                                'Cross River State' => 'Cross River State',
                                'Delta' => 'Delta',
                                'Ebonyi State' => 'Ebonyi State',
                                'Edo' => 'Edo',
                                'Ekiti State' => 'Ekiti State',
                                'Enugu State' => 'Enugu State',
                                'Federal Capital Territory' => 'Federal Capital Territory',
                                'Gombe State' => 'Gombe State',
                                'Imo State' => 'Imo State',
                                'Kaduna State' => 'Kaduna State',
                                'Kano State' => 'Kano State',
                                'Katsina State' => 'Katsina State',
                                'Kebbi State' => 'Kebbi State',
                                'Kogi State' => 'Kogi State',
                                'Kwara State' => 'Kwara State',
                                'Lagos' => 'Lagos',
                                'Nasarawa State' => 'Nasarawa State',
                                'Niger State' => 'Niger State',
                                'Ogun State' => 'Ogun State',
                                'Ondo State' => 'Ondo State',
                                'Osun State' => 'Osun State',
                                'Oyo State' => 'Oyo State',
                                'Plateau State' => 'Plateau State',
                                'Rivers State' => 'Rivers State',
                                'Sokoto State' => 'Sokoto State',
                                'Taraba State' => 'Taraba State',
                                'Yobe State' => 'Yobe State',
                                'Zamfara State' => 'Zamfara State',
                            ],
                    ],
                'NI' =>
                    [
                        'value' => 'NI',
                        'label' => 'Nicaragua',
                        'regions' =>
                            [
                                'Departamento de Boaco' => 'Departamento de Boaco',
                                'Departamento de Carazo' => 'Departamento de Carazo',
                                'Departamento de Chinandega' => 'Departamento de Chinandega',
                                'Departamento de Chontales' => 'Departamento de Chontales',
                                'Departamento de Esteli' => 'Departamento de Esteli',
                                'Departamento de Granada' => 'Departamento de Granada',
                                'Departamento de Jinotega' => 'Departamento de Jinotega',
                                'Departamento de Leon' => 'Departamento de Leon',
                                'Departamento de Managua' => 'Departamento de Managua',
                                'Departamento de Masaya' => 'Departamento de Masaya',
                                'Departamento de Matagalpa' => 'Departamento de Matagalpa',
                                'Departamento de Nueva Segovia' => 'Departamento de Nueva Segovia',
                                'Departamento de Rivas' => 'Departamento de Rivas',
                                'Region Autonoma Atlantico Sur' => 'Region Autonoma Atlantico Sur',
                            ],
                    ],
                'NL' =>
                    [
                        'value' => 'NL',
                        'label' => 'Netherlands',
                        'regions' =>
                            [
                                'Friesland' => 'Friesland',
                                'Groningen' => 'Groningen',
                                'Limburg' => 'Limburg',
                                'North Brabant' => 'North Brabant',
                                'North Holland' => 'North Holland',
                                'Provincie Drenthe' => 'Provincie Drenthe',
                                'Provincie Flevoland' => 'Provincie Flevoland',
                                'Provincie Gelderland' => 'Provincie Gelderland',
                                'Provincie Overijssel' => 'Provincie Overijssel',
                                'Provincie Utrecht' => 'Provincie Utrecht',
                                'Provincie Zeeland' => 'Provincie Zeeland',
                                'South Holland' => 'South Holland',
                            ],
                    ],
                'NO' =>
                    [
                        'value' => 'NO',
                        'label' => 'Norway',
                        'regions' =>
                            [
                                'Akershus' => 'Akershus',
                                'Aust-Agder' => 'Aust-Agder',
                                'Buskerud' => 'Buskerud',
                                'Finnmark Fylke' => 'Finnmark Fylke',
                                'Hedmark' => 'Hedmark',
                                'Hordaland Fylke' => 'Hordaland Fylke',
                                'More og Romsdal fylke' => 'More og Romsdal fylke',
                                'Nord-Trondelag Fylke' => 'Nord-Trondelag Fylke',
                                'Nordland Fylke' => 'Nordland Fylke',
                                'Oppland' => 'Oppland',
                                'Oslo County' => 'Oslo County',
                                'Rogaland Fylke' => 'Rogaland Fylke',
                                'Sogn og Fjordane Fylke' => 'Sogn og Fjordane Fylke',
                                'Sor-Trondelag Fylke' => 'Sor-Trondelag Fylke',
                                'Telemark' => 'Telemark',
                                'Troms Fylke' => 'Troms Fylke',
                                'Vest-Agder Fylke' => 'Vest-Agder Fylke',
                                'Vestfold' => 'Vestfold',
                                'Østfold' => 'Østfold',
                            ],
                    ],
                'NP' =>
                    [
                        'value' => 'NP',
                        'label' => 'Nepal',
                        'regions' =>
                            [
                                'Central Region' => 'Central Region',
                                'Eastern Region' => 'Eastern Region',
                                'Far Western' => 'Far Western',
                                'Western Region' => 'Western Region',
                            ],
                    ],
                'NR' =>
                    [
                        'value' => 'NR',
                        'label' => 'Nauru',
                        'regions' =>
                            [
                                'Anabar' => 'Anabar',
                            ],
                    ],
                'NU' =>
                    [
                        'value' => 'NU',
                        'label' => 'Niue',
                        'regions' =>
                            [
                            ],
                    ],
                'NZ' =>
                    [
                        'value' => 'NZ',
                        'label' => 'New Zealand',
                        'regions' =>
                            [
                                'Auckland' => 'Auckland',
                                'Bay of Plenty Region' => 'Bay of Plenty Region',
                                'Canterbury' => 'Canterbury',
                                'Chatham Islands' => 'Chatham Islands',
                                'Gisborne' => 'Gisborne',
                                'Hawke\'s Bay' => 'Hawke\'s Bay',
                                'Manawatu-Wanganui' => 'Manawatu-Wanganui',
                                'Marlborough' => 'Marlborough',
                                'Nelson' => 'Nelson',
                                'Northland Region' => 'Northland Region',
                                'Otago' => 'Otago',
                                'Southland' => 'Southland',
                                'Taranaki' => 'Taranaki',
                                'Tasman' => 'Tasman',
                                'Waikato' => 'Waikato',
                                'Wellington' => 'Wellington',
                                'West Coast' => 'West Coast',
                            ],
                    ],
                'OM' =>
                    [
                        'value' => 'OM',
                        'label' => 'Oman',
                        'regions' =>
                            [
                                'Al Batinah North Governorate' => 'Al Batinah North Governorate',
                                'Muhafazat Masqat' => 'Muhafazat Masqat',
                                'Muhafazat Zufar' => 'Muhafazat Zufar',
                                'Muhafazat ad Dakhiliyah' => 'Muhafazat ad Dakhiliyah',
                            ],
                    ],
                'PA' =>
                    [
                        'value' => 'PA',
                        'label' => 'Panama',
                        'regions' =>
                            [
                                'Embera-Wounaan' => 'Embera-Wounaan',
                                'Guna Yala' => 'Guna Yala',
                                'Ngoebe-Bugle' => 'Ngoebe-Bugle',
                                'Provincia de Bocas del Toro' => 'Provincia de Bocas del Toro',
                                'Provincia de Chiriqui' => 'Provincia de Chiriqui',
                                'Provincia de Cocle' => 'Provincia de Cocle',
                                'Provincia de Colon' => 'Provincia de Colon',
                                'Provincia de Herrera' => 'Provincia de Herrera',
                                'Provincia de Los Santos' => 'Provincia de Los Santos',
                                'Provincia de Panama' => 'Provincia de Panama',
                                'Provincia de Veraguas' => 'Provincia de Veraguas',
                                'Provincia del Darien' => 'Provincia del Darien',
                            ],
                    ],
                'PE' =>
                    [
                        'value' => 'PE',
                        'label' => 'Peru',
                        'regions' =>
                            [
                                'Amazonas' => 'Amazonas',
                                'Ancash' => 'Ancash',
                                'Apurimac' => 'Apurimac',
                                'Arequipa' => 'Arequipa',
                                'Ayacucho' => 'Ayacucho',
                                'Cajamarca' => 'Cajamarca',
                                'Callao' => 'Callao',
                                'Cusco' => 'Cusco',
                                'Departamento de Moquegua' => 'Departamento de Moquegua',
                                'Huancavelica' => 'Huancavelica',
                                'Ica' => 'Ica',
                                'Junín Region' => 'Junín Region',
                                'La Libertad' => 'La Libertad',
                                'Lambayeque' => 'Lambayeque',
                                'Lima region' => 'Lima region',
                                'Loreto' => 'Loreto',
                                'Pasco Region' => 'Pasco Region',
                                'Piura' => 'Piura',
                                'Provincia de Lima' => 'Provincia de Lima',
                                'Puno' => 'Puno',
                                'Region de Huanuco' => 'Region de Huanuco',
                                'Region de San Martin' => 'Region de San Martin',
                                'Tacna' => 'Tacna',
                                'Tumbes' => 'Tumbes',
                                'Ucayali' => 'Ucayali',
                            ],
                    ],
                'PF' =>
                    [
                        'value' => 'PF',
                        'label' => 'French Polynesia',
                        'regions' =>
                            [
                                'Iles du Vent' => 'Iles du Vent',
                                'Leeward Islands' => 'Leeward Islands',
                            ],
                    ],
                'PG' =>
                    [
                        'value' => 'PG',
                        'label' => 'Papua New Guinea',
                        'regions' =>
                            [
                                'Bougainville' => 'Bougainville',
                                'Central Province' => 'Central Province',
                                'Chimbu Province' => 'Chimbu Province',
                                'East New Britain Province' => 'East New Britain Province',
                                'East Sepik Province' => 'East Sepik Province',
                                'Eastern Highlands Province' => 'Eastern Highlands Province',
                                'Enga Province' => 'Enga Province',
                                'Gulf Province' => 'Gulf Province',
                                'Madang Province' => 'Madang Province',
                                'Manus Province' => 'Manus Province',
                                'Milne Bay Province' => 'Milne Bay Province',
                                'Morobe Province' => 'Morobe Province',
                                'National Capital' => 'National Capital',
                                'New Ireland' => 'New Ireland',
                                'Northern Province' => 'Northern Province',
                                'Southern Highlands Province' => 'Southern Highlands Province',
                                'West New Britain Province' => 'West New Britain Province',
                                'West Sepik Province' => 'West Sepik Province',
                                'Western Highlands Province' => 'Western Highlands Province',
                                'Western Province' => 'Western Province',
                            ],
                    ],
                'PH' =>
                    [
                        'value' => 'PH',
                        'label' => 'Philippines',
                        'regions' =>
                            [
                                'Autonomous Region in Muslim Mindanao' => 'Autonomous Region in Muslim Mindanao',
                                'Bicol' => 'Bicol',
                                'Cagayan Valley' => 'Cagayan Valley',
                                'Calabarzon' => 'Calabarzon',
                                'Caraga' => 'Caraga',
                                'Central Luzon' => 'Central Luzon',
                                'Central Visayas' => 'Central Visayas',
                                'Cordillera' => 'Cordillera',
                                'Davao' => 'Davao',
                                'Eastern Visayas' => 'Eastern Visayas',
                                'Ilocos' => 'Ilocos',
                                'Mimaropa' => 'Mimaropa',
                                'National Capital Region' => 'National Capital Region',
                                'Northern Mindanao' => 'Northern Mindanao',
                                'Soccsksargen' => 'Soccsksargen',
                                'Western Visayas' => 'Western Visayas',
                                'Zamboanga Peninsula' => 'Zamboanga Peninsula',
                            ],
                    ],
                'PK' =>
                    [
                        'value' => 'PK',
                        'label' => 'Pakistan',
                        'regions' =>
                            [
                                'Azad Kashmir' => 'Azad Kashmir',
                                'Balochistan' => 'Balochistan',
                                'Federally Administered Tribal Areas' => 'Federally Administered Tribal Areas',
                                'Gilgit-Baltistan' => 'Gilgit-Baltistan',
                                'Islamabad Capital Territory' => 'Islamabad Capital Territory',
                                'Khyber Pakhtunkhwa' => 'Khyber Pakhtunkhwa',
                                'Punjab' => 'Punjab',
                                'Sindh' => 'Sindh',
                            ],
                    ],
                'PL' =>
                    [
                        'value' => 'PL',
                        'label' => 'Poland',
                        'regions' =>
                            [
                                'Greater Poland Voivodeship' => 'Greater Poland Voivodeship',
                                'Kujawsko-Pomorskie' => 'Kujawsko-Pomorskie',
                                'Lesser Poland Voivodeship' => 'Lesser Poland Voivodeship',
                                'Lower Silesian Voivodeship' => 'Lower Silesian Voivodeship',
                                'Lublin Voivodeship' => 'Lublin Voivodeship',
                                'Lubusz' => 'Lubusz',
                                'Masovian Voivodeship' => 'Masovian Voivodeship',
                                'Opole Voivodeship' => 'Opole Voivodeship',
                                'Podlasie' => 'Podlasie',
                                'Pomeranian Voivodeship' => 'Pomeranian Voivodeship',
                                'Silesian Voivodeship' => 'Silesian Voivodeship',
                                'Subcarpathian Voivodeship' => 'Subcarpathian Voivodeship',
                                'Warmian-Masurian Voivodeship' => 'Warmian-Masurian Voivodeship',
                                'West Pomeranian Voivodeship' => 'West Pomeranian Voivodeship',
                                'Łódź Voivodeship' => 'Łódź Voivodeship',
                                'Świętokrzyskie' => 'Świętokrzyskie',
                            ],
                    ],
                'PM' =>
                    [
                        'value' => 'PM',
                        'label' => 'Saint Pierre and Miquelon',
                        'regions' =>
                            [
                                'Commune de Miquelon-Langlade' => 'Commune de Miquelon-Langlade',
                                'Commune de Saint-Pierre' => 'Commune de Saint-Pierre',
                            ],
                    ],
                'PN' =>
                    [
                        'value' => 'PN',
                        'label' => 'Pitcairn Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'PR' =>
                    [
                        'value' => 'PR',
                        'label' => 'Puerto Rico',
                        'regions' =>
                            [
                            ],
                    ],
                'PS' =>
                    [
                        'value' => 'PS',
                        'label' => 'Palestine',
                        'regions' =>
                            [
                            ],
                    ],
                'PT' =>
                    [
                        'value' => 'PT',
                        'label' => 'Portugal',
                        'regions' =>
                            [
                                'Aveiro' => 'Aveiro',
                                'Azores' => 'Azores',
                                'Beja' => 'Beja',
                                'Braga' => 'Braga',
                                'Bragança' => 'Bragança',
                                'Castelo Branco' => 'Castelo Branco',
                                'Coimbra' => 'Coimbra',
                                'Faro' => 'Faro',
                                'Guarda' => 'Guarda',
                                'Leiria' => 'Leiria',
                                'Lisbon' => 'Lisbon',
                                'Madeira' => 'Madeira',
                                'Portalegre' => 'Portalegre',
                                'Porto' => 'Porto',
                                'Santarém' => 'Santarém',
                                'Setúbal' => 'Setúbal',
                                'Viana do Castelo' => 'Viana do Castelo',
                                'Vila Real' => 'Vila Real',
                                'Viseu' => 'Viseu',
                                'Évora' => 'Évora',
                            ],
                    ],
                'PW' =>
                    [
                        'value' => 'PW',
                        'label' => 'Palau',
                        'regions' =>
                            [
                            ],
                    ],
                'PY' =>
                    [
                        'value' => 'PY',
                        'label' => 'Paraguay',
                        'regions' =>
                            [
                                'Asuncion' => 'Asuncion',
                                'Departamento Central' => 'Departamento Central',
                                'Departamento de Alto Paraguay' => 'Departamento de Alto Paraguay',
                                'Departamento de Boqueron' => 'Departamento de Boqueron',
                                'Departamento de Caazapa' => 'Departamento de Caazapa',
                                'Departamento de Itapua' => 'Departamento de Itapua',
                                'Departamento de Misiones' => 'Departamento de Misiones',
                                'Departamento de Paraguari' => 'Departamento de Paraguari',
                                'Departamento de la Cordillera' => 'Departamento de la Cordillera',
                                'Departamento del Alto Parana' => 'Departamento del Alto Parana',
                                'Departamento del Amambay' => 'Departamento del Amambay',
                            ],
                    ],
                'QA' =>
                    [
                        'value' => 'QA',
                        'label' => 'Qatar',
                        'regions' =>
                            [
                                'Al Wakrah' => 'Al Wakrah',
                                'Baladiyat Umm Salal' => 'Baladiyat Umm Salal',
                                'Baladiyat ad Dawhah' => 'Baladiyat ad Dawhah',
                                'Baladiyat al Khawr wa adh Dhakhirah' => 'Baladiyat al Khawr wa adh Dhakhirah',
                                'Baladiyat ar Rayyan' => 'Baladiyat ar Rayyan',
                                'Baladiyat ash Shamal' => 'Baladiyat ash Shamal',
                            ],
                    ],
                'RE' =>
                    [
                        'value' => 'RE',
                        'label' => 'Réunion',
                        'regions' =>
                            [
                            ],
                    ],
                'RO' =>
                    [
                        'value' => 'RO',
                        'label' => 'Romania',
                        'regions' =>
                            [
                                'Arad' => 'Arad',
                                'Bihor' => 'Bihor',
                                'Bucuresti' => 'Bucuresti',
                                'Constanta' => 'Constanta',
                                'Covasna' => 'Covasna',
                                'Dolj' => 'Dolj',
                                'Giurgiu' => 'Giurgiu',
                                'Gorj' => 'Gorj',
                                'Harghita' => 'Harghita',
                                'Hunedoara' => 'Hunedoara',
                                'Ilfov' => 'Ilfov',
                                'Judetul Alba' => 'Judetul Alba',
                                'Judetul Arges' => 'Judetul Arges',
                                'Judetul Bacau' => 'Judetul Bacau',
                                'Judetul Bistrita-Nasaud' => 'Judetul Bistrita-Nasaud',
                                'Judetul Botosani' => 'Judetul Botosani',
                                'Judetul Braila' => 'Judetul Braila',
                                'Judetul Brasov' => 'Judetul Brasov',
                                'Judetul Buzau' => 'Judetul Buzau',
                                'Judetul Calarasi' => 'Judetul Calarasi',
                                'Judetul Caras-Severin' => 'Judetul Caras-Severin',
                                'Judetul Cluj' => 'Judetul Cluj',
                                'Judetul Dambovita' => 'Judetul Dambovita',
                                'Judetul Galati' => 'Judetul Galati',
                                'Judetul Ialomita' => 'Judetul Ialomita',
                                'Judetul Iasi' => 'Judetul Iasi',
                                'Judetul Mehedinti' => 'Judetul Mehedinti',
                                'Judetul Mures' => 'Judetul Mures',
                                'Judetul Neamt' => 'Judetul Neamt',
                                'Judetul Salaj' => 'Judetul Salaj',
                                'Judetul Sibiu' => 'Judetul Sibiu',
                                'Judetul Timis' => 'Judetul Timis',
                                'Judetul Valcea' => 'Judetul Valcea',
                                'Maramureş' => 'Maramureş',
                                'Olt County' => 'Olt County',
                                'Prahova' => 'Prahova',
                                'Satu Mare' => 'Satu Mare',
                                'Suceava' => 'Suceava',
                                'Teleorman' => 'Teleorman',
                                'Tulcea' => 'Tulcea',
                                'Vaslui' => 'Vaslui',
                                'Vrancea' => 'Vrancea',
                            ],
                    ],
                'RS' =>
                    [
                        'value' => 'RS',
                        'label' => 'Serbia',
                        'regions' =>
                            [
                                'Vojvodina' => 'Vojvodina',
                            ],
                    ],
                'RU' =>
                    [
                        'value' => 'RU',
                        'label' => 'Russia',
                        'regions' =>
                            [
                                'Altai Krai' => 'Altai Krai',
                                'Altai Republic' => 'Altai Republic',
                                'Amurskaya Oblast\'' => 'Amurskaya Oblast\'',
                                'Arkhangelskaya' => 'Arkhangelskaya',
                                'Astrakhanskaya Oblast\'' => 'Astrakhanskaya Oblast\'',
                                'Bashkortostan' => 'Bashkortostan',
                                'Belgorodskaya Oblast\'' => 'Belgorodskaya Oblast\'',
                                'Bryanskaya Oblast\'' => 'Bryanskaya Oblast\'',
                                'Chechnya' => 'Chechnya',
                                'Chelyabinsk' => 'Chelyabinsk',
                                'Chukotskiy Avtonomnyy Okrug' => 'Chukotskiy Avtonomnyy Okrug',
                                'Chuvashia' => 'Chuvashia',
                                'Dagestan' => 'Dagestan',
                                'Irkutskaya Oblast\'' => 'Irkutskaya Oblast\'',
                                'Ivanovskaya Oblast\'' => 'Ivanovskaya Oblast\'',
                                'Jewish Autonomous Oblast' => 'Jewish Autonomous Oblast',
                                'Kabardino-Balkarskaya Respublika' => 'Kabardino-Balkarskaya Respublika',
                                'Kaliningradskaya Oblast\'' => 'Kaliningradskaya Oblast\'',
                                'Kalmykiya' => 'Kalmykiya',
                                'Kaluzhskaya Oblast\'' => 'Kaluzhskaya Oblast\'',
                                'Kamtchatski Kray' => 'Kamtchatski Kray',
                                'Karachayevo-Cherkesiya' => 'Karachayevo-Cherkesiya',
                                'Kemerovskaya Oblast\'' => 'Kemerovskaya Oblast\'',
                                'Khabarovsk Krai' => 'Khabarovsk Krai',
                                'Khanty-Mansiyskiy Avtonomnyy Okrug-Yugra' => 'Khanty-Mansiyskiy Avtonomnyy Okrug-Yugra',
                                'Kirovskaya Oblast\'' => 'Kirovskaya Oblast\'',
                                'Komi Republic' => 'Komi Republic',
                                'Kostromskaya Oblast\'' => 'Kostromskaya Oblast\'',
                                'Krasnodarskiy Kray' => 'Krasnodarskiy Kray',
                                'Krasnoyarskiy Kray' => 'Krasnoyarskiy Kray',
                                'Kurganskaya Oblast\'' => 'Kurganskaya Oblast\'',
                                'Kurskaya Oblast\'' => 'Kurskaya Oblast\'',
                                'Leningradskaya Oblast\'' => 'Leningradskaya Oblast\'',
                                'Lipetskaya Oblast\'' => 'Lipetskaya Oblast\'',
                                'Magadanskaya Oblast\'' => 'Magadanskaya Oblast\'',
                                'Moscow' => 'Moscow',
                                'Moscow Oblast' => 'Moscow Oblast',
                                'Murmansk' => 'Murmansk',
                                'Nenetskiy Avtonomnyy Okrug' => 'Nenetskiy Avtonomnyy Okrug',
                                'Nizhegorodskaya Oblast\'' => 'Nizhegorodskaya Oblast\'',
                                'North Ossetia' => 'North Ossetia',
                                'Novgorodskaya Oblast\'' => 'Novgorodskaya Oblast\'',
                                'Novosibirskaya Oblast\'' => 'Novosibirskaya Oblast\'',
                                'Omskaya Oblast\'' => 'Omskaya Oblast\'',
                                'Orenburgskaya Oblast\'' => 'Orenburgskaya Oblast\'',
                                'Orlovskaya Oblast\'' => 'Orlovskaya Oblast\'',
                                'Penzenskaya Oblast\'' => 'Penzenskaya Oblast\'',
                                'Perm Krai' => 'Perm Krai',
                                'Primorskiy Kray' => 'Primorskiy Kray',
                                'Pskovskaya Oblast\'' => 'Pskovskaya Oblast\'',
                                'Republic of Karelia' => 'Republic of Karelia',
                                'Respublika Adygeya' => 'Respublika Adygeya',
                                'Respublika Buryatiya' => 'Respublika Buryatiya',
                                'Respublika Ingushetiya' => 'Respublika Ingushetiya',
                                'Respublika Khakasiya' => 'Respublika Khakasiya',
                                'Respublika Mariy-El' => 'Respublika Mariy-El',
                                'Respublika Mordoviya' => 'Respublika Mordoviya',
                                'Respublika Sakha (Yakutiya)' => 'Respublika Sakha (Yakutiya)',
                                'Respublika Tyva' => 'Respublika Tyva',
                                'Rostov Oblast' => 'Rostov Oblast',
                                'Ryazanskaya Oblast\'' => 'Ryazanskaya Oblast\'',
                                'Sakhalinskaya Oblast\'' => 'Sakhalinskaya Oblast\'',
                                'Samarskaya Oblast\'' => 'Samarskaya Oblast\'',
                                'Saratovskaya Oblast\'' => 'Saratovskaya Oblast\'',
                                'Smolenskaya Oblast\'' => 'Smolenskaya Oblast\'',
                                'St.-Petersburg' => 'St.-Petersburg',
                                'Stavropol\'skiy Kray' => 'Stavropol\'skiy Kray',
                                'Sverdlovskaya Oblast\'' => 'Sverdlovskaya Oblast\'',
                                'Tambovskaya Oblast\'' => 'Tambovskaya Oblast\'',
                                'Tatarstan' => 'Tatarstan',
                                'Tomskaya Oblast\'' => 'Tomskaya Oblast\'',
                                'Transbaikal Territory' => 'Transbaikal Territory',
                                'Tul\'skaya Oblast\'' => 'Tul\'skaya Oblast\'',
                                'Tverskaya Oblast\'' => 'Tverskaya Oblast\'',
                                'Tyumenskaya Oblast\'' => 'Tyumenskaya Oblast\'',
                                'Udmurtskaya Respublika' => 'Udmurtskaya Respublika',
                                'Ulyanovsk Oblast' => 'Ulyanovsk Oblast',
                                'Vladimirskaya Oblast\'' => 'Vladimirskaya Oblast\'',
                                'Volgogradskaya Oblast\'' => 'Volgogradskaya Oblast\'',
                                'Vologodskaya Oblast\'' => 'Vologodskaya Oblast\'',
                                'Voronezhskaya Oblast\'' => 'Voronezhskaya Oblast\'',
                                'Yamalo-Nenetskiy Avtonomnyy Okrug' => 'Yamalo-Nenetskiy Avtonomnyy Okrug',
                                'Yaroslavskaya Oblast\'' => 'Yaroslavskaya Oblast\'',
                            ],
                    ],
                'RW' =>
                    [
                        'value' => 'RW',
                        'label' => 'Rwanda',
                        'regions' =>
                            [
                                'Kigali' => 'Kigali',
                            ],
                    ],
                'SA' =>
                    [
                        'value' => 'SA',
                        'label' => 'Saudi Arabia',
                        'regions' =>
                            [
                                '\'Asir' => '\'Asir',
                                'Al Bahah' => 'Al Bahah',
                                'Al Madinah al Munawwarah' => 'Al Madinah al Munawwarah',
                                'Al-Qassim' => 'Al-Qassim',
                                'Ar Riyāḑ' => 'Ar Riyāḑ',
                                'Eastern Province' => 'Eastern Province',
                                'Hai\'l Region' => 'Hai\'l Region',
                                'Jizan' => 'Jizan',
                                'Makkah Province' => 'Makkah Province',
                                'Najran' => 'Najran',
                                'Tabuk' => 'Tabuk',
                            ],
                    ],
                'SB' =>
                    [
                        'value' => 'SB',
                        'label' => 'Solomon Islands',
                        'regions' =>
                            [
                                'Guadalcanal Province' => 'Guadalcanal Province',
                            ],
                    ],
                'SC' =>
                    [
                        'value' => 'SC',
                        'label' => 'Seychelles',
                        'regions' =>
                            [
                                'English River' => 'English River',
                                'Takamaka' => 'Takamaka',
                            ],
                    ],
                'SD' =>
                    [
                        'value' => 'SD',
                        'label' => 'Sudan',
                        'regions' =>
                            [
                                'Khartoum' => 'Khartoum',
                                'Southern Kordofan' => 'Southern Kordofan',
                            ],
                    ],
                'SE' =>
                    [
                        'value' => 'SE',
                        'label' => 'Sweden',
                        'regions' =>
                            [
                                'Blekinge' => 'Blekinge',
                                'Dalarna' => 'Dalarna',
                                'Gotland' => 'Gotland',
                                'Gävleborg' => 'Gävleborg',
                                'Halland' => 'Halland',
                                'Jämtland' => 'Jämtland',
                                'Jönköping' => 'Jönköping',
                                'Kalmar' => 'Kalmar',
                                'Kronoberg' => 'Kronoberg',
                                'Norrbotten' => 'Norrbotten',
                                'Skåne' => 'Skåne',
                                'Stockholm' => 'Stockholm',
                                'Södermanland' => 'Södermanland',
                                'Uppsala' => 'Uppsala',
                                'Värmland' => 'Värmland',
                                'Västerbotten' => 'Västerbotten',
                                'Västernorrland' => 'Västernorrland',
                                'Västmanland' => 'Västmanland',
                                'Västra Götaland' => 'Västra Götaland',
                                'Örebro' => 'Örebro',
                                'Östergötland' => 'Östergötland',
                            ],
                    ],
                'SG' =>
                    [
                        'value' => 'SG',
                        'label' => 'Singapore',
                        'regions' =>
                            [
                                'Central Singapore Community Development Council' => 'Central Singapore Community Development Council',
                                'North East Community Development Region' => 'North East Community Development Region',
                                'North West Community Development Council' => 'North West Community Development Council',
                                'South West Community Development Council' => 'South West Community Development Council',
                            ],
                    ],
                'SH' =>
                    [
                        'value' => 'SH',
                        'label' => 'Saint Helena',
                        'regions' =>
                            [
                            ],
                    ],
                'SI' =>
                    [
                        'value' => 'SI',
                        'label' => 'Slovenia',
                        'regions' =>
                            [
                                'Beltinci' => 'Beltinci',
                                'Bohinj' => 'Bohinj',
                                'Borovnica' => 'Borovnica',
                                'Brda' => 'Brda',
                                'Brezovica' => 'Brezovica',
                                'Cankova' => 'Cankova',
                                'Celje' => 'Celje',
                                'Cerknica' => 'Cerknica',
                                'Cerkno' => 'Cerkno',
                                'Cerkvenjak' => 'Cerkvenjak',
                                'Cirkulane' => 'Cirkulane',
                                'Destrnik' => 'Destrnik',
                                'Dobrova-Polhov Gradec' => 'Dobrova-Polhov Gradec',
                                'Dol pri Ljubljani' => 'Dol pri Ljubljani',
                                'Dolenjske Toplice' => 'Dolenjske Toplice',
                                'Dravograd' => 'Dravograd',
                                'Duplek' => 'Duplek',
                                'Gorenja Vas-Poljane' => 'Gorenja Vas-Poljane',
                                'Gornja Radgona' => 'Gornja Radgona',
                                'Gornji Grad' => 'Gornji Grad',
                                'Gornji Petrovci' => 'Gornji Petrovci',
                                'Grosuplje' => 'Grosuplje',
                                'Hajdina' => 'Hajdina',
                                'Horjul' => 'Horjul',
                                'Hrastnik' => 'Hrastnik',
                                'Hrpelje-Kozina' => 'Hrpelje-Kozina',
                                'Idrija' => 'Idrija',
                                'Ig' => 'Ig',
                                'Ilirska Bistrica' => 'Ilirska Bistrica',
                                'Izola' => 'Izola',
                                'Jesenice' => 'Jesenice',
                                'Kamnik' => 'Kamnik',
                                'Komen' => 'Komen',
                                'Koper' => 'Koper',
                                'Kostanjevica na Krki' => 'Kostanjevica na Krki',
                                'Kostel' => 'Kostel',
                                'Kranj' => 'Kranj',
                                'Kranjska Gora' => 'Kranjska Gora',
                                'Kuzma' => 'Kuzma',
                                'Lenart' => 'Lenart',
                                'Lendava' => 'Lendava',
                                'Litija' => 'Litija',
                                'Ljubljana' => 'Ljubljana',
                                'Ljubno' => 'Ljubno',
                                'Ljutomer' => 'Ljutomer',
                                'Logatec' => 'Logatec',
                                'Log–Dragomer' => 'Log–Dragomer',
                                'Lovrenc na Pohorju' => 'Lovrenc na Pohorju',
                                'Lukovica' => 'Lukovica',
                                'Makole' => 'Makole',
                                'Maribor' => 'Maribor',
                                'Markovci' => 'Markovci',
                                'Medvode' => 'Medvode',
                                'Mestna Obcina Novo mesto' => 'Mestna Obcina Novo mesto',
                                'Metlika' => 'Metlika',
                                'Miren-Kostanjevica' => 'Miren-Kostanjevica',
                                'Mislinja' => 'Mislinja',
                                'Mokronog-Trebelno' => 'Mokronog-Trebelno',
                                'Mozirje' => 'Mozirje',
                                'Municipality of Cerklje na Gorenjskem' => 'Municipality of Cerklje na Gorenjskem',
                                'Municipality of Dobrna' => 'Municipality of Dobrna',
                                'Municipality of Šentjur' => 'Municipality of Šentjur',
                                'Murska Sobota' => 'Murska Sobota',
                                'Naklo' => 'Naklo',
                                'Nova Gorica' => 'Nova Gorica',
                                'Obcina Ajdovscina' => 'Obcina Ajdovscina',
                                'Obcina Apace' => 'Obcina Apace',
                                'Obcina Bled' => 'Obcina Bled',
                                'Obcina Brezice' => 'Obcina Brezice',
                                'Obcina Crna na Koroskem' => 'Obcina Crna na Koroskem',
                                'Obcina Crnomelj' => 'Obcina Crnomelj',
                                'Obcina Domzale' => 'Obcina Domzale',
                                'Obcina Gorisnica' => 'Obcina Gorisnica',
                                'Obcina Hoce-Slivnica' => 'Obcina Hoce-Slivnica',
                                'Obcina Ivancna Gorica' => 'Obcina Ivancna Gorica',
                                'Obcina Jursinci' => 'Obcina Jursinci',
                                'Obcina Kidricevo' => 'Obcina Kidricevo',
                                'Obcina Kobarid' => 'Obcina Kobarid',
                                'Obcina Kocevje' => 'Obcina Kocevje',
                                'Obcina Krsko' => 'Obcina Krsko',
                                'Obcina Lasko' => 'Obcina Lasko',
                                'Obcina Loska Dolina' => 'Obcina Loska Dolina',
                                'Obcina Majsperk' => 'Obcina Majsperk',
                                'Obcina Menges' => 'Obcina Menges',
                                'Obcina Mezica' => 'Obcina Mezica',
                                'Obcina Miklavz na Dravskem Polju' => 'Obcina Miklavz na Dravskem Polju',
                                'Obcina Moravce' => 'Obcina Moravce',
                                'Obcina Ormoz' => 'Obcina Ormoz',
                                'Obcina Poljcane' => 'Obcina Poljcane',
                                'Obcina Race-Fram' => 'Obcina Race-Fram',
                                'Obcina Radece' => 'Obcina Radece',
                                'Obcina Ravne na Koroskem' => 'Obcina Ravne na Koroskem',
                                'Obcina Razkrizje' => 'Obcina Razkrizje',
                                'Obcina Recica ob Savinji' => 'Obcina Recica ob Savinji',
                                'Obcina Rogaska Slatina' => 'Obcina Rogaska Slatina',
                                'Obcina Rogasovci' => 'Obcina Rogasovci',
                                'Obcina Ruse' => 'Obcina Ruse',
                                'Obcina Semic' => 'Obcina Semic',
                                'Obcina Sempeter-Vrtojba' => 'Obcina Sempeter-Vrtojba',
                                'Obcina Sencur' => 'Obcina Sencur',
                                'Obcina Sentilj' => 'Obcina Sentilj',
                                'Obcina Sentjernej' => 'Obcina Sentjernej',
                                'Obcina Sezana' => 'Obcina Sezana',
                                'Obcina Skofljica' => 'Obcina Skofljica',
                                'Obcina Smartno ob Paki' => 'Obcina Smartno ob Paki',
                                'Obcina Smartno pri Litiji' => 'Obcina Smartno pri Litiji',
                                'Obcina Sostanj' => 'Obcina Sostanj',
                                'Obcina Store' => 'Obcina Store',
                                'Obcina Straza' => 'Obcina Straza',
                                'Obcina Tisina' => 'Obcina Tisina',
                                'Obcina Tolmin' => 'Obcina Tolmin',
                                'Obcina Trzic' => 'Obcina Trzic',
                                'Obcina Velike Lasce' => 'Obcina Velike Lasce',
                                'Obcina Zalec' => 'Obcina Zalec',
                                'Obcina Zelezniki' => 'Obcina Zelezniki',
                                'Obcina Zirovnica' => 'Obcina Zirovnica',
                                'Obcina Zrece' => 'Obcina Zrece',
                                'Obcina Zuzemberk' => 'Obcina Zuzemberk',
                                'Odranci' => 'Odranci',
                                'Osilnica' => 'Osilnica',
                                'Pesnica' => 'Pesnica',
                                'Piran' => 'Piran',
                                'Pivka' => 'Pivka',
                                'Podlehnik' => 'Podlehnik',
                                'Polzela' => 'Polzela',
                                'Postojna' => 'Postojna',
                                'Preddvor' => 'Preddvor',
                                'Prevalje' => 'Prevalje',
                                'Ptuj' => 'Ptuj',
                                'Puconci' => 'Puconci',
                                'Radlje ob Dravi' => 'Radlje ob Dravi',
                                'Radovljica' => 'Radovljica',
                                'Ribnica' => 'Ribnica',
                                'Selnica ob Dravi' => 'Selnica ob Dravi',
                                'Sevnica' => 'Sevnica',
                                'Slovenj Gradec' => 'Slovenj Gradec',
                                'Slovenska Bistrica' => 'Slovenska Bistrica',
                                'Slovenske Konjice' => 'Slovenske Konjice',
                                'Sveta Ana' => 'Sveta Ana',
                                'Tabor' => 'Tabor',
                                'Trbovlje' => 'Trbovlje',
                                'Trebnje' => 'Trebnje',
                                'Trzin' => 'Trzin',
                                'Velenje' => 'Velenje',
                                'Videm' => 'Videm',
                                'Vipava' => 'Vipava',
                                'Vransko' => 'Vransko',
                                'Zagorje ob Savi' => 'Zagorje ob Savi',
                                'Škofja Loka' => 'Škofja Loka',
                            ],
                    ],
                'SJ' =>
                    [
                        'value' => 'SJ',
                        'label' => 'Svalbard and Jan Mayen',
                        'regions' =>
                            [
                                'Jan Mayen' => 'Jan Mayen',
                                'Svalbard' => 'Svalbard',
                            ],
                    ],
                'SK' =>
                    [
                        'value' => 'SK',
                        'label' => 'Slovak Republic',
                        'regions' =>
                            [
                                'Banskobystricky kraj' => 'Banskobystricky kraj',
                                'Bratislavsky kraj' => 'Bratislavsky kraj',
                                'Kosicky kraj' => 'Kosicky kraj',
                                'Nitriansky kraj' => 'Nitriansky kraj',
                                'Presovsky kraj' => 'Presovsky kraj',
                                'Trenciansky kraj' => 'Trenciansky kraj',
                                'Trnavsky kraj' => 'Trnavsky kraj',
                                'Zilinsky kraj' => 'Zilinsky kraj',
                            ],
                    ],
                'SL' =>
                    [
                        'value' => 'SL',
                        'label' => 'Sierra Leone',
                        'regions' =>
                            [
                                'Western Area' => 'Western Area',
                            ],
                    ],
                'SM' =>
                    [
                        'value' => 'SM',
                        'label' => 'San Marino',
                        'regions' =>
                            [
                                'Castello di Borgo Maggiore' => 'Castello di Borgo Maggiore',
                                'Castello di Faetano' => 'Castello di Faetano',
                                'Castello di San Marino Citta' => 'Castello di San Marino Citta',
                                'Serravalle' => 'Serravalle',
                            ],
                    ],
                'SN' =>
                    [
                        'value' => 'SN',
                        'label' => 'Senegal',
                        'regions' =>
                            [
                                'Dakar' => 'Dakar',
                                'Fatick' => 'Fatick',
                                'Kaolack' => 'Kaolack',
                                'Kolda' => 'Kolda',
                                'Louga' => 'Louga',
                                'Region de Kaffrine' => 'Region de Kaffrine',
                                'Region de Kedougou' => 'Region de Kedougou',
                                'Region de Sedhiou' => 'Region de Sedhiou',
                                'Saint-Louis' => 'Saint-Louis',
                                'Tambacounda' => 'Tambacounda',
                            ],
                    ],
                'SO' =>
                    [
                        'value' => 'SO',
                        'label' => 'Somalia',
                        'regions' =>
                            [
                                'Banaadir' => 'Banaadir',
                                'Gedo' => 'Gedo',
                                'Woqooyi Galbeed' => 'Woqooyi Galbeed',
                            ],
                    ],
                'SR' =>
                    [
                        'value' => 'SR',
                        'label' => 'Suriname',
                        'regions' =>
                            [
                                'Distrikt Brokopondo' => 'Distrikt Brokopondo',
                                'Distrikt Commewijne' => 'Distrikt Commewijne',
                                'Distrikt Coronie' => 'Distrikt Coronie',
                                'Distrikt Marowijne' => 'Distrikt Marowijne',
                                'Distrikt Nickerie' => 'Distrikt Nickerie',
                                'Distrikt Para' => 'Distrikt Para',
                                'Distrikt Paramaribo' => 'Distrikt Paramaribo',
                                'Distrikt Saramacca' => 'Distrikt Saramacca',
                                'Distrikt Sipaliwini' => 'Distrikt Sipaliwini',
                                'Distrikt Wanica' => 'Distrikt Wanica',
                            ],
                    ],
                'SS' =>
                    [
                        'value' => 'SS',
                        'label' => 'South Sudan',
                        'regions' =>
                            [
                                'Central Equatoria' => 'Central Equatoria',
                            ],
                    ],
                'ST' =>
                    [
                        'value' => 'ST',
                        'label' => 'São Tomé and Príncipe',
                        'regions' =>
                            [
                                'Principe' => 'Principe',
                                'São Tomé Island' => 'São Tomé Island',
                            ],
                    ],
                'SV' =>
                    [
                        'value' => 'SV',
                        'label' => 'El Salvador',
                        'regions' =>
                            [
                                'Departamento de Ahuachapan' => 'Departamento de Ahuachapan',
                                'Departamento de Cabanas' => 'Departamento de Cabanas',
                                'Departamento de Chalatenango' => 'Departamento de Chalatenango',
                                'Departamento de Cuscatlan' => 'Departamento de Cuscatlan',
                                'Departamento de La Libertad' => 'Departamento de La Libertad',
                                'Departamento de La Paz' => 'Departamento de La Paz',
                                'Departamento de La Union' => 'Departamento de La Union',
                                'Departamento de Morazan' => 'Departamento de Morazan',
                                'Departamento de San Miguel' => 'Departamento de San Miguel',
                                'Departamento de San Salvador' => 'Departamento de San Salvador',
                                'Departamento de San Vicente' => 'Departamento de San Vicente',
                                'Departamento de Santa Ana' => 'Departamento de Santa Ana',
                                'Departamento de Sonsonate' => 'Departamento de Sonsonate',
                                'Departamento de Usulutan' => 'Departamento de Usulutan',
                            ],
                    ],
                'SX' =>
                    [
                        'value' => 'SX',
                        'label' => 'Sint Maarten',
                        'regions' =>
                            [
                            ],
                    ],
                'SY' =>
                    [
                        'value' => 'SY',
                        'label' => 'Syria',
                        'regions' =>
                            [
                                'Aleppo Governorate' => 'Aleppo Governorate',
                                'As-Suwayda Governorate' => 'As-Suwayda Governorate',
                                'Damascus Governorate' => 'Damascus Governorate',
                                'Hama Governorate' => 'Hama Governorate',
                                'Latakia Governorate' => 'Latakia Governorate',
                                'Quneitra Governorate' => 'Quneitra Governorate',
                            ],
                    ],
                'SZ' =>
                    [
                        'value' => 'SZ',
                        'label' => 'Swaziland',
                        'regions' =>
                            [
                                'Hhohho District' => 'Hhohho District',
                                'Lubombo District' => 'Lubombo District',
                                'Manzini District' => 'Manzini District',
                            ],
                    ],
                'TC' =>
                    [
                        'value' => 'TC',
                        'label' => 'Turks and Caicos Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'TD' =>
                    [
                        'value' => 'TD',
                        'label' => 'Chad',
                        'regions' =>
                            [
                                'Chari-Baguirmi Region' => 'Chari-Baguirmi Region',
                                'Hadjer-Lamis' => 'Hadjer-Lamis',
                                'Logone Occidental Region' => 'Logone Occidental Region',
                                'Ouadaï' => 'Ouadaï',
                            ],
                    ],
                'TF' =>
                    [
                        'value' => 'TF',
                        'label' => 'French Southern Territories',
                        'regions' =>
                            [
                            ],
                    ],
                'TG' =>
                    [
                        'value' => 'TG',
                        'label' => 'Togo',
                        'regions' =>
                            [
                                'Maritime' => 'Maritime',
                            ],
                    ],
                'TH' =>
                    [
                        'value' => 'TH',
                        'label' => 'Thailand',
                        'regions' =>
                            [
                                'Bangkok' => 'Bangkok',
                                'Changwat Amnat Charoen' => 'Changwat Amnat Charoen',
                                'Changwat Ang Thong' => 'Changwat Ang Thong',
                                'Changwat Bueng Kan' => 'Changwat Bueng Kan',
                                'Changwat Buriram' => 'Changwat Buriram',
                                'Changwat Chachoengsao' => 'Changwat Chachoengsao',
                                'Changwat Chai Nat' => 'Changwat Chai Nat',
                                'Changwat Chaiyaphum' => 'Changwat Chaiyaphum',
                                'Changwat Chanthaburi' => 'Changwat Chanthaburi',
                                'Changwat Chiang Rai' => 'Changwat Chiang Rai',
                                'Changwat Chon Buri' => 'Changwat Chon Buri',
                                'Changwat Chumphon' => 'Changwat Chumphon',
                                'Changwat Kalasin' => 'Changwat Kalasin',
                                'Changwat Kamphaeng Phet' => 'Changwat Kamphaeng Phet',
                                'Changwat Kanchanaburi' => 'Changwat Kanchanaburi',
                                'Changwat Khon Kaen' => 'Changwat Khon Kaen',
                                'Changwat Krabi' => 'Changwat Krabi',
                                'Changwat Lampang' => 'Changwat Lampang',
                                'Changwat Lamphun' => 'Changwat Lamphun',
                                'Changwat Loei' => 'Changwat Loei',
                                'Changwat Lop Buri' => 'Changwat Lop Buri',
                                'Changwat Mae Hong Son' => 'Changwat Mae Hong Son',
                                'Changwat Maha Sarakham' => 'Changwat Maha Sarakham',
                                'Changwat Mukdahan' => 'Changwat Mukdahan',
                                'Changwat Nakhon Nayok' => 'Changwat Nakhon Nayok',
                                'Changwat Nakhon Pathom' => 'Changwat Nakhon Pathom',
                                'Changwat Nakhon Phanom' => 'Changwat Nakhon Phanom',
                                'Changwat Nakhon Ratchasima' => 'Changwat Nakhon Ratchasima',
                                'Changwat Nakhon Sawan' => 'Changwat Nakhon Sawan',
                                'Changwat Nakhon Si Thammarat' => 'Changwat Nakhon Si Thammarat',
                                'Changwat Nan' => 'Changwat Nan',
                                'Changwat Narathiwat' => 'Changwat Narathiwat',
                                'Changwat Nong Bua Lamphu' => 'Changwat Nong Bua Lamphu',
                                'Changwat Nong Khai' => 'Changwat Nong Khai',
                                'Changwat Nonthaburi' => 'Changwat Nonthaburi',
                                'Changwat Pathum Thani' => 'Changwat Pathum Thani',
                                'Changwat Pattani' => 'Changwat Pattani',
                                'Changwat Phangnga' => 'Changwat Phangnga',
                                'Changwat Phatthalung' => 'Changwat Phatthalung',
                                'Changwat Phayao' => 'Changwat Phayao',
                                'Changwat Phetchabun' => 'Changwat Phetchabun',
                                'Changwat Phetchaburi' => 'Changwat Phetchaburi',
                                'Changwat Phichit' => 'Changwat Phichit',
                                'Changwat Phitsanulok' => 'Changwat Phitsanulok',
                                'Changwat Phra Nakhon Si Ayutthaya' => 'Changwat Phra Nakhon Si Ayutthaya',
                                'Changwat Phrae' => 'Changwat Phrae',
                                'Changwat Prachin Buri' => 'Changwat Prachin Buri',
                                'Changwat Prachuap Khiri Khan' => 'Changwat Prachuap Khiri Khan',
                                'Changwat Ranong' => 'Changwat Ranong',
                                'Changwat Ratchaburi' => 'Changwat Ratchaburi',
                                'Changwat Rayong' => 'Changwat Rayong',
                                'Changwat Roi Et' => 'Changwat Roi Et',
                                'Changwat Sa Kaeo' => 'Changwat Sa Kaeo',
                                'Changwat Sakon Nakhon' => 'Changwat Sakon Nakhon',
                                'Changwat Samut Prakan' => 'Changwat Samut Prakan',
                                'Changwat Samut Sakhon' => 'Changwat Samut Sakhon',
                                'Changwat Samut Songkhram' => 'Changwat Samut Songkhram',
                                'Changwat Sara Buri' => 'Changwat Sara Buri',
                                'Changwat Satun' => 'Changwat Satun',
                                'Changwat Sing Buri' => 'Changwat Sing Buri',
                                'Changwat Sisaket' => 'Changwat Sisaket',
                                'Changwat Songkhla' => 'Changwat Songkhla',
                                'Changwat Sukhothai' => 'Changwat Sukhothai',
                                'Changwat Suphan Buri' => 'Changwat Suphan Buri',
                                'Changwat Surat Thani' => 'Changwat Surat Thani',
                                'Changwat Surin' => 'Changwat Surin',
                                'Changwat Tak' => 'Changwat Tak',
                                'Changwat Trang' => 'Changwat Trang',
                                'Changwat Trat' => 'Changwat Trat',
                                'Changwat Ubon Ratchathani' => 'Changwat Ubon Ratchathani',
                                'Changwat Udon Thani' => 'Changwat Udon Thani',
                                'Changwat Uthai Thani' => 'Changwat Uthai Thani',
                                'Changwat Uttaradit' => 'Changwat Uttaradit',
                                'Changwat Yala' => 'Changwat Yala',
                                'Changwat Yasothon' => 'Changwat Yasothon',
                                'Chiang Mai Province' => 'Chiang Mai Province',
                                'Phuket' => 'Phuket',
                            ],
                    ],
                'TJ' =>
                    [
                        'value' => 'TJ',
                        'label' => 'Tajikistan',
                        'regions' =>
                            [
                                'Gorno-Badakhshan' => 'Gorno-Badakhshan',
                                'Viloyati Sughd' => 'Viloyati Sughd',
                            ],
                    ],
                'TK' =>
                    [
                        'value' => 'TK',
                        'label' => 'Tokelau',
                        'regions' =>
                            [
                                'Nukunonu' => 'Nukunonu',
                            ],
                    ],
                'TL' =>
                    [
                        'value' => 'TL',
                        'label' => 'East Timor',
                        'regions' =>
                            [
                                'Dili' => 'Dili',
                            ],
                    ],
                'TM' =>
                    [
                        'value' => 'TM',
                        'label' => 'Turkmenistan',
                        'regions' =>
                            [
                                'Ahal' => 'Ahal',
                            ],
                    ],
                'TN' =>
                    [
                        'value' => 'TN',
                        'label' => 'Tunisia',
                        'regions' =>
                            [
                                'Gafsa' => 'Gafsa',
                                'Gouvernorat de Beja' => 'Gouvernorat de Beja',
                                'Gouvernorat de Ben Arous' => 'Gouvernorat de Ben Arous',
                                'Gouvernorat de Bizerte' => 'Gouvernorat de Bizerte',
                                'Gouvernorat de Gabes' => 'Gouvernorat de Gabes',
                                'Gouvernorat de Kairouan' => 'Gouvernorat de Kairouan',
                                'Gouvernorat de Kasserine' => 'Gouvernorat de Kasserine',
                                'Gouvernorat de Kef' => 'Gouvernorat de Kef',
                                'Gouvernorat de Mahdia' => 'Gouvernorat de Mahdia',
                                'Gouvernorat de Monastir' => 'Gouvernorat de Monastir',
                                'Gouvernorat de Nabeul' => 'Gouvernorat de Nabeul',
                                'Gouvernorat de Sfax' => 'Gouvernorat de Sfax',
                                'Gouvernorat de Sidi Bouzid' => 'Gouvernorat de Sidi Bouzid',
                                'Gouvernorat de Siliana' => 'Gouvernorat de Siliana',
                                'Gouvernorat de Sousse' => 'Gouvernorat de Sousse',
                                'Gouvernorat de Tozeur' => 'Gouvernorat de Tozeur',
                                'Gouvernorat de Tunis' => 'Gouvernorat de Tunis',
                                'Gouvernorat de Zaghouan' => 'Gouvernorat de Zaghouan',
                                'Gouvernorat de l\'Ariana' => 'Gouvernorat de l\'Ariana',
                                'Tataouine' => 'Tataouine',
                            ],
                    ],
                'TO' =>
                    [
                        'value' => 'TO',
                        'label' => 'Tonga',
                        'regions' =>
                            [
                                'Vava\'u' => 'Vava\'u',
                            ],
                    ],
                'TR' =>
                    [
                        'value' => 'TR',
                        'label' => 'Turkey',
                        'regions' =>
                            [
                                'Adana' => 'Adana',
                                'Adiyaman' => 'Adiyaman',
                                'Afyonkarahisar' => 'Afyonkarahisar',
                                'Aksaray' => 'Aksaray',
                                'Amasya' => 'Amasya',
                                'Ankara' => 'Ankara',
                                'Antalya' => 'Antalya',
                                'Ardahan' => 'Ardahan',
                                'Artvin' => 'Artvin',
                                'Aydın' => 'Aydın',
                                'Ağrı' => 'Ağrı',
                                'Balıkesir' => 'Balıkesir',
                                'Bartın' => 'Bartın',
                                'Batman' => 'Batman',
                                'Bayburt' => 'Bayburt',
                                'Bilecik' => 'Bilecik',
                                'Bingöl' => 'Bingöl',
                                'Bitlis' => 'Bitlis',
                                'Bolu' => 'Bolu',
                                'Burdur' => 'Burdur',
                                'Bursa' => 'Bursa',
                                'Denizli' => 'Denizli',
                                'Diyarbakir' => 'Diyarbakir',
                                'Duezce' => 'Duezce',
                                'Edirne' => 'Edirne',
                                'Elazığ' => 'Elazığ',
                                'Erzincan' => 'Erzincan',
                                'Erzurum' => 'Erzurum',
                                'Eskişehir' => 'Eskişehir',
                                'Gaziantep' => 'Gaziantep',
                                'Giresun' => 'Giresun',
                                'Guemueshane' => 'Guemueshane',
                                'Hakkâri' => 'Hakkâri',
                                'Hatay' => 'Hatay',
                                'Isparta' => 'Isparta',
                                'Istanbul' => 'Istanbul',
                                'Izmir' => 'Izmir',
                                'Iğdır' => 'Iğdır',
                                'Kahramanmaraş' => 'Kahramanmaraş',
                                'Karabuek' => 'Karabuek',
                                'Karaman' => 'Karaman',
                                'Kars' => 'Kars',
                                'Kastamonu' => 'Kastamonu',
                                'Kayseri' => 'Kayseri',
                                'Kilis' => 'Kilis',
                                'Kocaeli' => 'Kocaeli',
                                'Konya' => 'Konya',
                                'Kütahya' => 'Kütahya',
                                'Kırklareli' => 'Kırklareli',
                                'Kırıkkale' => 'Kırıkkale',
                                'Kırşehir' => 'Kırşehir',
                                'Malatya' => 'Malatya',
                                'Manisa' => 'Manisa',
                                'Mardin' => 'Mardin',
                                'Mersin' => 'Mersin',
                                'Muğla' => 'Muğla',
                                'Muş' => 'Muş',
                                'Nevsehir' => 'Nevsehir',
                                'Nigde' => 'Nigde',
                                'Ordu' => 'Ordu',
                                'Osmaniye' => 'Osmaniye',
                                'Rize' => 'Rize',
                                'Sakarya' => 'Sakarya',
                                'Samsun' => 'Samsun',
                                'Siirt' => 'Siirt',
                                'Sinop' => 'Sinop',
                                'Sivas' => 'Sivas',
                                'Tekirdağ' => 'Tekirdağ',
                                'Tokat' => 'Tokat',
                                'Trabzon' => 'Trabzon',
                                'Tunceli' => 'Tunceli',
                                'Uşak' => 'Uşak',
                                'Van' => 'Van',
                                'Yalova' => 'Yalova',
                                'Yozgat' => 'Yozgat',
                                'Zonguldak' => 'Zonguldak',
                                'Çanakkale' => 'Çanakkale',
                                'Çankırı' => 'Çankırı',
                                'Çorum' => 'Çorum',
                                'Şanlıurfa' => 'Şanlıurfa',
                                'Şırnak' => 'Şırnak',
                            ],
                    ],
                'TT' =>
                    [
                        'value' => 'TT',
                        'label' => 'Trinidad and Tobago',
                        'regions' =>
                            [
                                'Borough of Arima' => 'Borough of Arima',
                                'Chaguanas' => 'Chaguanas',
                                'City of Port of Spain' => 'City of Port of Spain',
                                'City of San Fernando' => 'City of San Fernando',
                                'Couva-Tabaquite-Talparo' => 'Couva-Tabaquite-Talparo',
                                'Diego Martin' => 'Diego Martin',
                                'Eastern Tobago' => 'Eastern Tobago',
                                'Mayaro' => 'Mayaro',
                                'Penal/Debe' => 'Penal/Debe',
                                'Point Fortin' => 'Point Fortin',
                                'Princes Town' => 'Princes Town',
                                'San Juan/Laventille' => 'San Juan/Laventille',
                                'Sangre Grande' => 'Sangre Grande',
                                'Siparia' => 'Siparia',
                                'Tobago' => 'Tobago',
                                'Tunapuna/Piarco' => 'Tunapuna/Piarco',
                            ],
                    ],
                'TV' =>
                    [
                        'value' => 'TV',
                        'label' => 'Tuvalu',
                        'regions' =>
                            [
                                'Funafuti' => 'Funafuti',
                                'Vaitupu' => 'Vaitupu',
                            ],
                    ],
                'TW' =>
                    [
                        'value' => 'TW',
                        'label' => 'Taiwan',
                        'regions' =>
                            [
                                'Changhua' => 'Changhua',
                                'Chiayi' => 'Chiayi',
                                'Hsinchu' => 'Hsinchu',
                                'Hsinchu County' => 'Hsinchu County',
                                'Hualien' => 'Hualien',
                                'Kaohsiung' => 'Kaohsiung',
                                'Keelung' => 'Keelung',
                                'Miaoli' => 'Miaoli',
                                'Nantou' => 'Nantou',
                                'New Taipei' => 'New Taipei',
                                'Pingtung' => 'Pingtung',
                                'Taichung City' => 'Taichung City',
                                'Tainan' => 'Tainan',
                                'Taitung' => 'Taitung',
                                'Taoyuan' => 'Taoyuan',
                                'Yilan' => 'Yilan',
                                'Yunlin County' => 'Yunlin County',
                            ],
                    ],
                'TZ' =>
                    [
                        'value' => 'TZ',
                        'label' => 'Tanzania',
                        'regions' =>
                            [
                                'Arusha' => 'Arusha',
                                'Dar es Salaam Region' => 'Dar es Salaam Region',
                                'Dodoma' => 'Dodoma',
                                'Iringa' => 'Iringa',
                                'Kagera' => 'Kagera',
                                'Kigoma' => 'Kigoma',
                                'Kilimanjaro' => 'Kilimanjaro',
                                'Lindi' => 'Lindi',
                                'Manyara' => 'Manyara',
                                'Mara' => 'Mara',
                                'Mbeya' => 'Mbeya',
                                'Morogoro' => 'Morogoro',
                                'Mtwara' => 'Mtwara',
                                'Mwanza' => 'Mwanza',
                                'Pemba North' => 'Pemba North',
                                'Pemba South' => 'Pemba South',
                                'Pwani' => 'Pwani',
                                'Rukwa' => 'Rukwa',
                                'Ruvuma' => 'Ruvuma',
                                'Shinyanga' => 'Shinyanga',
                                'Singida' => 'Singida',
                                'Tabora' => 'Tabora',
                                'Tanga' => 'Tanga',
                                'Zanzibar Central/South' => 'Zanzibar Central/South',
                                'Zanzibar North' => 'Zanzibar North',
                                'Zanzibar Urban/West' => 'Zanzibar Urban/West',
                            ],
                    ],
                'UA' =>
                    [
                        'value' => 'UA',
                        'label' => 'Ukraine',
                        'regions' =>
                            [
                                'Cherkas\'ka Oblast\'' => 'Cherkas\'ka Oblast\'',
                                'Chernihiv' => 'Chernihiv',
                                'Chernivtsi' => 'Chernivtsi',
                                'Dnipropetrovska Oblast\'' => 'Dnipropetrovska Oblast\'',
                                'Donets\'ka Oblast\'' => 'Donets\'ka Oblast\'',
                                'Gorod Sevastopol' => 'Gorod Sevastopol',
                                'Ivano-Frankivs\'ka Oblast\'' => 'Ivano-Frankivs\'ka Oblast\'',
                                'Kharkivs\'ka Oblast\'' => 'Kharkivs\'ka Oblast\'',
                                'Khersons\'ka Oblast\'' => 'Khersons\'ka Oblast\'',
                                'Khmel\'nyts\'ka Oblast\'' => 'Khmel\'nyts\'ka Oblast\'',
                                'Kirovohrads\'ka Oblast\'' => 'Kirovohrads\'ka Oblast\'',
                                'Kyiv City' => 'Kyiv City',
                                'Kyiv Oblast' => 'Kyiv Oblast',
                                'L\'vivs\'ka Oblast\'' => 'L\'vivs\'ka Oblast\'',
                                'Luhans\'ka Oblast\'' => 'Luhans\'ka Oblast\'',
                                'Mykolayivs\'ka Oblast\'' => 'Mykolayivs\'ka Oblast\'',
                                'Odessa' => 'Odessa',
                                'Poltavs\'ka Oblast\'' => 'Poltavs\'ka Oblast\'',
                                'Republic of Crimea' => 'Republic of Crimea',
                                'Rivnens\'ka Oblast\'' => 'Rivnens\'ka Oblast\'',
                                'Sums\'ka Oblast\'' => 'Sums\'ka Oblast\'',
                                'Ternopil\'s\'ka Oblast\'' => 'Ternopil\'s\'ka Oblast\'',
                                'Vinnyts\'ka Oblast\'' => 'Vinnyts\'ka Oblast\'',
                                'Volyns\'ka Oblast\'' => 'Volyns\'ka Oblast\'',
                                'Zakarpattia Oblast' => 'Zakarpattia Oblast',
                                'Zaporizhia' => 'Zaporizhia',
                                'Zhytomyrs\'ka Oblast\'' => 'Zhytomyrs\'ka Oblast\'',
                            ],
                    ],
                'UG' =>
                    [
                        'value' => 'UG',
                        'label' => 'Uganda',
                        'regions' =>
                            [
                                'Central Region' => 'Central Region',
                            ],
                    ],
                'UM' =>
                    [
                        'value' => 'UM',
                        'label' => 'U.S. Minor Outlying Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'US' =>
                    [
                        'value' => 'US',
                        'label' => 'United States',
                        'regions' =>
                            [
                                'Alabama' => 'Alabama',
                                'Alaska' => 'Alaska',
                                'Arizona' => 'Arizona',
                                'Arkansas' => 'Arkansas',
                                'California' => 'California',
                                'Colorado' => 'Colorado',
                                'Connecticut' => 'Connecticut',
                                'Delaware' => 'Delaware',
                                'District of Columbia' => 'District of Columbia',
                                'Florida' => 'Florida',
                                'Georgia' => 'Georgia',
                                'Hawaii' => 'Hawaii',
                                'Idaho' => 'Idaho',
                                'Illinois' => 'Illinois',
                                'Indiana' => 'Indiana',
                                'Iowa' => 'Iowa',
                                'Kansas' => 'Kansas',
                                'Kentucky' => 'Kentucky',
                                'Louisiana' => 'Louisiana',
                                'Maine' => 'Maine',
                                'Maryland' => 'Maryland',
                                'Massachusetts' => 'Massachusetts',
                                'Michigan' => 'Michigan',
                                'Minnesota' => 'Minnesota',
                                'Mississippi' => 'Mississippi',
                                'Missouri' => 'Missouri',
                                'Montana' => 'Montana',
                                'Nebraska' => 'Nebraska',
                                'Nevada' => 'Nevada',
                                'New Hampshire' => 'New Hampshire',
                                'New Jersey' => 'New Jersey',
                                'New Mexico' => 'New Mexico',
                                'New York' => 'New York',
                                'North Carolina' => 'North Carolina',
                                'North Dakota' => 'North Dakota',
                                'Ohio' => 'Ohio',
                                'Oklahoma' => 'Oklahoma',
                                'Oregon' => 'Oregon',
                                'Pennsylvania' => 'Pennsylvania',
                                'Rhode Island' => 'Rhode Island',
                                'South Carolina' => 'South Carolina',
                                'South Dakota' => 'South Dakota',
                                'Tennessee' => 'Tennessee',
                                'Texas' => 'Texas',
                                'Utah' => 'Utah',
                                'Vermont' => 'Vermont',
                                'Virginia' => 'Virginia',
                                'Washington' => 'Washington',
                                'West Virginia' => 'West Virginia',
                                'Wisconsin' => 'Wisconsin',
                                'Wyoming' => 'Wyoming',
                            ],
                    ],
                'UY' =>
                    [
                        'value' => 'UY',
                        'label' => 'Uruguay',
                        'regions' =>
                            [
                                'Artigas' => 'Artigas',
                                'Canelones' => 'Canelones',
                                'Cerro Largo' => 'Cerro Largo',
                                'Colonia' => 'Colonia',
                                'Departamento de Montevideo' => 'Departamento de Montevideo',
                                'Departamento de Paysandu' => 'Departamento de Paysandu',
                                'Departamento de Rio Negro' => 'Departamento de Rio Negro',
                                'Departamento de Rivera' => 'Departamento de Rivera',
                                'Departamento de San Jose' => 'Departamento de San Jose',
                                'Departamento de Tacuarembo' => 'Departamento de Tacuarembo',
                                'Florida' => 'Florida',
                                'Lavalleja' => 'Lavalleja',
                                'Maldonado' => 'Maldonado',
                                'Soriano' => 'Soriano',
                            ],
                    ],
                'UZ' =>
                    [
                        'value' => 'UZ',
                        'label' => 'Uzbekistan',
                        'regions' =>
                            [
                                'Qashqadaryo' => 'Qashqadaryo',
                                'Samarqand Viloyati' => 'Samarqand Viloyati',
                                'Toshkent Shahri' => 'Toshkent Shahri',
                            ],
                    ],
                'VA' =>
                    [
                        'value' => 'VA',
                        'label' => 'Vatican City',
                        'regions' =>
                            [
                            ],
                    ],
                'VC' =>
                    [
                        'value' => 'VC',
                        'label' => 'Saint Vincent and the Grenadines',
                        'regions' =>
                            [
                                'Grenadines' => 'Grenadines',
                                'Parish of Charlotte' => 'Parish of Charlotte',
                                'Parish of Saint George' => 'Parish of Saint George',
                            ],
                    ],
                'VE' =>
                    [
                        'value' => 'VE',
                        'label' => 'Venezuela',
                        'regions' =>
                            [
                                'Amazonas' => 'Amazonas',
                                'Anzoátegui' => 'Anzoátegui',
                                'Apure' => 'Apure',
                                'Aragua' => 'Aragua',
                                'Barinas' => 'Barinas',
                                'Bolívar' => 'Bolívar',
                                'Capital' => 'Capital',
                                'Carabobo' => 'Carabobo',
                                'Cojedes' => 'Cojedes',
                                'Delta Amacuro' => 'Delta Amacuro',
                                'Dependencias Federales' => 'Dependencias Federales',
                                'Estado Trujillo' => 'Estado Trujillo',
                                'Falcón' => 'Falcón',
                                'Guárico' => 'Guárico',
                                'Lara' => 'Lara',
                                'Miranda' => 'Miranda',
                                'Monagas' => 'Monagas',
                                'Mérida' => 'Mérida',
                                'Nueva Esparta' => 'Nueva Esparta',
                                'Portuguesa' => 'Portuguesa',
                                'Sucre' => 'Sucre',
                                'Táchira' => 'Táchira',
                                'Vargas' => 'Vargas',
                                'Yaracuy' => 'Yaracuy',
                                'Zulia' => 'Zulia',
                            ],
                    ],
                'VG' =>
                    [
                        'value' => 'VG',
                        'label' => 'British Virgin Islands',
                        'regions' =>
                            [
                            ],
                    ],
                'VI' =>
                    [
                        'value' => 'VI',
                        'label' => 'U.S. Virgin Islands',
                        'regions' =>
                            [
                                'Saint Croix Island' => 'Saint Croix Island',
                                'Saint John Island' => 'Saint John Island',
                                'Saint Thomas Island' => 'Saint Thomas Island',
                            ],
                    ],
                'VN' =>
                    [
                        'value' => 'VN',
                        'label' => 'Vietnam',
                        'regions' =>
                            [
                                'An Giang' => 'An Giang',
                                'Dak Nong' => 'Dak Nong',
                                'Gia Lai' => 'Gia Lai',
                                'Hau Giang' => 'Hau Giang',
                                'Ho Chi Minh City' => 'Ho Chi Minh City',
                                'Kon Tum' => 'Kon Tum',
                                'Long An' => 'Long An',
                                'Thanh Pho Can Tho' => 'Thanh Pho Can Tho',
                                'Thanh Pho GJa Nang' => 'Thanh Pho GJa Nang',
                                'Thanh Pho Ha Noi' => 'Thanh Pho Ha Noi',
                                'Thanh Pho Hai Phong' => 'Thanh Pho Hai Phong',
                                'Tinh Ba Ria-Vung Tau' => 'Tinh Ba Ria-Vung Tau',
                                'Tinh Bac Giang' => 'Tinh Bac Giang',
                                'Tinh Bac Lieu' => 'Tinh Bac Lieu',
                                'Tinh Bac Ninh' => 'Tinh Bac Ninh',
                                'Tinh Ben Tre' => 'Tinh Ben Tre',
                                'Tinh Binh Duong' => 'Tinh Binh Duong',
                                'Tinh Binh GJinh' => 'Tinh Binh GJinh',
                                'Tinh Binh Thuan' => 'Tinh Binh Thuan',
                                'Tinh Ca Mau' => 'Tinh Ca Mau',
                                'Tinh Cao Bang' => 'Tinh Cao Bang',
                                'Tinh Dien Bien' => 'Tinh Dien Bien',
                                'Tinh GJak Lak' => 'Tinh GJak Lak',
                                'Tinh GJong Nai' => 'Tinh GJong Nai',
                                'Tinh GJong Thap' => 'Tinh GJong Thap',
                                'Tinh Ha Giang' => 'Tinh Ha Giang',
                                'Tinh Ha Nam' => 'Tinh Ha Nam',
                                'Tinh Ha Tinh' => 'Tinh Ha Tinh',
                                'Tinh Hai Duong' => 'Tinh Hai Duong',
                                'Tinh Hoa Binh' => 'Tinh Hoa Binh',
                                'Tinh Hung Yen' => 'Tinh Hung Yen',
                                'Tinh Khanh Hoa' => 'Tinh Khanh Hoa',
                                'Tinh Kien Giang' => 'Tinh Kien Giang',
                                'Tinh Lai Chau' => 'Tinh Lai Chau',
                                'Tinh Lam GJong' => 'Tinh Lam GJong',
                                'Tinh Lang Son' => 'Tinh Lang Son',
                                'Tinh Lao Cai' => 'Tinh Lao Cai',
                                'Tinh Nam GJinh' => 'Tinh Nam GJinh',
                                'Tinh Nghe An' => 'Tinh Nghe An',
                                'Tinh Ninh Binh' => 'Tinh Ninh Binh',
                                'Tinh Phu Tho' => 'Tinh Phu Tho',
                                'Tinh Phu Yen' => 'Tinh Phu Yen',
                                'Tinh Quang Binh' => 'Tinh Quang Binh',
                                'Tinh Quang Nam' => 'Tinh Quang Nam',
                                'Tinh Quang Ngai' => 'Tinh Quang Ngai',
                                'Tinh Quang Tri' => 'Tinh Quang Tri',
                                'Tinh Soc Trang' => 'Tinh Soc Trang',
                                'Tinh Son La' => 'Tinh Son La',
                                'Tinh Tay Ninh' => 'Tinh Tay Ninh',
                                'Tinh Thai Binh' => 'Tinh Thai Binh',
                                'Tinh Thai Nguyen' => 'Tinh Thai Nguyen',
                                'Tinh Thanh Hoa' => 'Tinh Thanh Hoa',
                                'Tinh Thua Thien-Hue' => 'Tinh Thua Thien-Hue',
                                'Tinh Tien Giang' => 'Tinh Tien Giang',
                                'Tinh Tra Vinh' => 'Tinh Tra Vinh',
                                'Tinh Tuyen Quang' => 'Tinh Tuyen Quang',
                                'Tinh Vinh Long' => 'Tinh Vinh Long',
                                'Tinh Vinh Phuc' => 'Tinh Vinh Phuc',
                                'Tinh Yen Bai' => 'Tinh Yen Bai',
                            ],
                    ],
                'VU' =>
                    [
                        'value' => 'VU',
                        'label' => 'Vanuatu',
                        'regions' =>
                            [
                                'Penama Province' => 'Penama Province',
                                'Shefa Province' => 'Shefa Province',
                            ],
                    ],
                'WF' =>
                    [
                        'value' => 'WF',
                        'label' => 'Wallis and Futuna',
                        'regions' =>
                            [
                            ],
                    ],
                'WS' =>
                    [
                        'value' => 'WS',
                        'label' => 'Samoa',
                        'regions' =>
                            [
                                'Atua' => 'Atua',
                                'Tuamasaga' => 'Tuamasaga',
                            ],
                    ],
                'XK' =>
                    [
                        'value' => 'XK',
                        'label' => 'Kosovo',
                        'regions' =>
                            [
                            ],
                    ],
                'YE' =>
                    [
                        'value' => 'YE',
                        'label' => 'Yemen',
                        'regions' =>
                            [
                                'Aden' => 'Aden',
                                'Dhamār' => 'Dhamār',
                                'Muhafazat Hadramawt' => 'Muhafazat Hadramawt',
                                'Muhafazat al Hudaydah' => 'Muhafazat al Hudaydah',
                                'Sanaa' => 'Sanaa',
                            ],
                    ],
                'YT' =>
                    [
                        'value' => 'YT',
                        'label' => 'Mayotte',
                        'regions' =>
                            [
                            ],
                    ],
                'ZA' =>
                    [
                        'value' => 'ZA',
                        'label' => 'South Africa',
                        'regions' =>
                            [
                                'Eastern Cape' => 'Eastern Cape',
                                'Gauteng' => 'Gauteng',
                                'KwaZulu-Natal' => 'KwaZulu-Natal',
                                'Limpopo' => 'Limpopo',
                                'Mpumalanga' => 'Mpumalanga',
                                'Northern Cape' => 'Northern Cape',
                                'Orange Free State' => 'Orange Free State',
                                'Province of North West' => 'Province of North West',
                                'Province of the Western Cape' => 'Province of the Western Cape',
                            ],
                    ],
                'ZM' =>
                    [
                        'value' => 'ZM',
                        'label' => 'Zambia',
                        'regions' =>
                            [
                                'Copperbelt' => 'Copperbelt',
                                'Luapula Province' => 'Luapula Province',
                                'Lusaka Province' => 'Lusaka Province',
                                'Muchinga Province' => 'Muchinga Province',
                                'North-Western Province' => 'North-Western Province',
                                'Northern Province' => 'Northern Province',
                                'Southern Province' => 'Southern Province',
                                'Western Province' => 'Western Province',
                            ],
                    ],
                'ZW' =>
                    [
                        'value' => 'ZW',
                        'label' => 'Zimbabwe',
                        'regions' =>
                            [
                                'Bulawayo' => 'Bulawayo',
                                'Harare' => 'Harare',
                                'Mashonaland West' => 'Mashonaland West',
                                'Matabeleland North' => 'Matabeleland North',
                                'Matabeleland South Province' => 'Matabeleland South Province',
                                'Midlands Province' => 'Midlands Province',
                            ],
                    ]
            ];

        return $data;
    }
}