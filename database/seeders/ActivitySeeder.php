<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Services\FileStorageService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        if (Activity::query()->exists()) {
            return;
        }

        $activities = [
            [
                'image' => 'siguldas-skati-todo-1.jpg',
                'title' => ['lv' => 'Dabas takas', 'en' => 'Nature trails'],
                'modal_heading' => [
                    'lv' => 'Siguldas dabas takas – piedzīvojums jebkurā gadalaikā',
                    'en' => 'Sigulda nature trails – an adventure in every season',
                ],
                'modal_content' => [
                    'lv' => '<p>Sigulda ir viena no Latvijas ainaviskākajām vietām, kur daba atklājas visā krāšņumā. Pilsētu ieskauj Gaujas Nacionālā parka takas, kas piemērotas gan mierīgām pastaigām, gan aktīvai atpūtai. Te iespējams izbaudīt elpu aizraujošus skatus no Gaujas senlejas kraujām, šķērsot vēsturiskos tiltiņus, iepazīt alas un avotus, kā arī doties maršrutos, kas ved cauri mežiem, pļavām un gar upes krastiem.</p>'
                        .'<p>Populārākās takas ir Paradīzes taka, Kājnieku tilta maršruts, Krimuldas dabas taka un Velnalas taka – katra no tām piedāvā unikālu pieredzi un savas ainavas. Pavasarī un vasarā tās ir pilnas ar zaļumu un ziediem, rudenī – krāsainu lapu jūru, bet ziemā pārtop klusā, sniegotā pasakā.</p>',
                    'en' => '<p>Sigulda is one of the most scenic places in Latvia, where nature reveals its beauty in full. The town is surrounded by the trails of Gauja National Park, suitable both for peaceful walks and active recreation. Here you can enjoy breathtaking views from the cliffs of the Gauja valley, cross historic bridges, explore caves and springs, and follow routes that lead through forests, meadows, and along the riverbanks.</p>'
                        .'<p>The most popular trails include the Paradise Trail, the Footbridge Route, the Krimulda Nature Trail, and the Devil’s Trail – each offering a unique experience and its own landscapes. In spring and summer, they are filled with greenery and blossoms; in autumn, a sea of colorful leaves; and in winter, they transform into a quiet, snowy fairytale.</p>',
                ],
            ],
            [
                'image' => 'siguldas-skati-todo-2.jpg',
                'title' => ['lv' => 'Aktīvā atpūta', 'en' => 'Active recreation'],
                'modal_heading' => [
                    'lv' => 'Siguldas aktīvā atpūta – piedzīvojumi ikvienam',
                    'en' => 'Active recreation in Sigulda – adventures for everyone',
                ],
                'modal_content' => [
                    'lv' => '<p>Sigulda ir Latvijas aktīvās atpūtas galvaspilsēta, kur piedzīvojumi pieejami visa gada garumā. Vasarā pilsēta piedāvā iespēju izmēģināt trošu un rodeļu nobraucienus piedzīvojuma parkā “Tarzāns” gaisa tramvaju pāri Gaujai, braucienus ar SUP dēļiem, kā arī velomaršrutus dažādās grūtības pakāpēs. Īpašu adrenalīna devu nodrošina bobsleja un kamaniņu trase, kur iespējams izjust ātrumu gan vasarā, gan ziemā.</p>'
                        .'<p>Rudenī un pavasarī Sigulda piesaista pārgājienu entuziastus ar krāšņām Gaujas Nacionālā parka takām, bet ziemā pilsēta pārtop par slēpošanas un snovborda centru ar vairākām trasēm un aprīkojuma nomu.</p>'
                        .'<p>Neatkarīgi no sezonas Siguldā iespējams apvienot aktīvu atpūtu ar kultūras un dabas baudījumu, radot neaizmirstamu pieredzi ikvienam viesim.</p>',
                    'en' => '<p>Sigulda is Latvia’s capital of active recreation, where adventures await all year round. In summer, the city offers the chance to try zipline and alpine coaster rides in the “Tarzāns” adventure park, cross the Gauja River by aerial cable car, go paddleboarding, or enjoy cycling routes of various difficulty levels. For a true adrenaline rush, Sigulda’s bobsleigh and luge track provides thrilling speed both in summer and winter.</p>'
                        .'<p>In autumn and spring, Sigulda attracts hiking enthusiasts with the colorful trails of Gauja National Park, while in winter the city transforms into a ski and snowboard hub with several slopes and equipment rental.</p>'
                        .'<p>Whatever the season, Sigulda offers the perfect combination of active leisure, nature, and culture – creating unforgettable experiences for every visitor.</p>',
                ],
            ],
            [
                'image' => 'siguldas-skati-todo-3.jpg',
                'title' => ['lv' => 'Garšu pieredze', 'en' => 'Taste experience'],
                'modal_heading' => [
                    'lv' => 'Siguldas kafejnīcas un restorāni – garšu ceļojums pilsētas sirdī',
                    'en' => 'Sigulda’s cafés and restaurants – a journey of flavors in the heart of the city',
                ],
                'modal_content' => [
                    'lv' => '<p>Sigulda piedāvā daudzveidīgu gastronomisko pieredzi – no mājīgām kafejnīcām līdz izsmalcinātiem restorāniem. Pilsētas centrā atrodami gan bistro ar sezonāliem ēdieniem, gan vietas, kas specializējas tradicionālajā latviešu virtuvē. Gardēžus priecēs arī desertu kafejnīcas, kurās iespējams nogaršot vietējo konditoru darinājumus.</p>'
                        .'<p>Vakara noskaņām piemēroti ir restorāni ar plašu vīnu un kokteiļu izvēli. Savukārt mierīgai pēcpusdienai ideāli iederēsies nelielas kafejnīcas, kur baudīt svaigi grauzdētu kafiju un mājās gatavotus desertus.</p>'
                        .'<p>Siguldas gastronomiskā ainava apvieno vietējo raksturu ar starptautiskām garšu tendencēm, piedāvājot izvēles iespējas ikvienai gaumei un noskaņojumam – gan ātrām maltītēm pēc aktīvās atpūtas, gan nesteidzīgām vakariņām īpašā atmosfērā.</p>'
                        .'<p>Iesakām apmeklēt: ESI cafe (Pils 4b), Aparjods (Ventas iela 1A), Lielais Loms suši (Krišjāņa Valdemāra iela 2).</p>',
                    'en' => '<p>Sigulda offers a diverse gastronomic experience – from cozy cafés to refined restaurants. In the city center, you’ll find bistros with seasonal dishes as well as places specializing in traditional Latvian cuisine. Food lovers will also enjoy dessert cafés, where local confectioners showcase their sweet creations.</p>'
                        .'<p>For an evening atmosphere, restaurants with a wide selection of wines and cocktails are a perfect choice. Meanwhile, for a relaxed afternoon, small cafés serving freshly roasted coffee and homemade desserts are an ideal fit.</p>'
                        .'<p>Sigulda’s culinary scene blends local character with international trends, offering options for every taste and mood – whether it’s a quick meal after an active day or an unhurried dinner in a special atmosphere.</p>'
                        .'<p>Recommended places to visit: ESI cafe (Pils 4b), Aparjods (Ventas Street 1A), Lielais Loms Sushi (Krišjāņa Valdemāra Street 2).</p>',
                ],
            ],
            [
                'image' => 'siguldas-skati-todo-4.jpg',
                'title' => ['lv' => 'Kultūra un vēsture', 'en' => 'Culture and history'],
                'modal_heading' => [
                    'lv' => 'Siguldas vēsture un kultūras dzīve',
                    'en' => 'Sigulda’s history and cultural life',
                ],
                'modal_content' => [
                    'lv' => '<p>Sigulda, bieži dēvēta par “Vidzemes Šveici”, ir pilsēta ar bagātu vēsturi un izteiktu kultūras identitāti. Pirmās rakstītās ziņas par Siguldu datētas ar 13. gadsimtu, kad tika uzcelts Siguldas viduslaiku pils komplekss, vēlāk papildināts ar Turaidas pili un Krimuldas muižu. Šie vēsturiskie objekti šodien ir nozīmīgas kultūras un tūrisma vietas, kas stāsta par reģiona attīstību vairāk nekā astoņu gadsimtu garumā.</p>'
                        .'<p>Sigulda vienmēr bijusi kultūras un mākslas centrs, kur regulāri notiek mūzikas festivāli, teātra izrādes, tautas tradīciju pasākumi un mākslas izstādes. Īpaši izceļas Siguldas Opermūzikas svētki un vasaras koncerti pilsdrupās, kas apvieno unikālu vidi ar augstvērtīgu muzikālo programmu.</p>'
                        .'<p>Mūsdienās pilsēta harmoniski apvieno senatnes mantojumu ar mūsdienīgu kultūras dzīvi, piedāvājot gan vietējiem iedzīvotājiem, gan viesiem daudzveidīgas iespējas izzināt vēsturi, baudīt mākslu un piedalīties radošos notikumos. No viduslaiku pilsdrupām līdz laikmetīgajām mākslas instalācijām – Sigulda ir vieta, kur vēsture un kultūra dzīvo līdzās un papildina viena otru.</p>',
                    'en' => '<p>Sigulda, often called the “Switzerland of Vidzeme,” is a town with a rich history and a strong cultural identity. The first written records of Sigulda date back to the 13th century, when the Sigulda medieval castle complex was built, later joined by Turaida Castle and Krimulda Manor. Today, these historic landmarks are important cultural and tourist sites, telling the story of the region’s development over more than eight centuries.</p>'
                        .'<p>Sigulda has always been a center of culture and the arts, regularly hosting music festivals, theater performances, folk tradition events, and art exhibitions. Of particular note are the Sigulda Opera Music Festival and the summer concerts held in the castle ruins, which combine a unique setting with a high-quality musical program.</p>'
                        .'<p>Today, the town harmoniously blends its historical heritage with a vibrant cultural life, offering both locals and visitors diverse opportunities to explore history, enjoy art, and take part in creative events. From medieval castle ruins to contemporary art installations – Sigulda is a place where history and culture coexist and enrich one another.</p>',
                ],
            ],
        ];

        foreach ($activities as $index => $data) {
            $activity = new Activity([
                'image' => $this->seedImage($data['image']),
                'order' => $index,
                'is_active' => true,
            ]);
            $activity->setTranslations('title', $data['title']);
            $activity->setTranslations('modal_heading', $data['modal_heading']);
            $activity->setTranslations('modal_content', $data['modal_content']);
            $activity->save();
        }
    }

    private function seedImage(string $filename): ?string
    {
        $storagePath = FileStorageService::ACTIVITY_IMAGE_PATH.'/'.$filename;
        $sourcePath = public_path('images/'.$filename);

        if (File::exists($sourcePath)) {
            Storage::disk('public')->put($storagePath, File::get($sourcePath));
        }

        return $storagePath;
    }
}
