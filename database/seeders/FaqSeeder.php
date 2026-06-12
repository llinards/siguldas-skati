<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        if (Faq::query()->exists()) {
            return;
        }

        $faqs = [
            [
                'question' => [
                    'lv' => 'Kur atrodas Siguldas Skati?',
                    'en' => 'Where is Siguldas Skati located?',
                ],
                'answer' => [
                    'lv' => '<p>Mūsu brīvdienu mājas atrodas Siguldas centrā, Cēsu ielā 17, blakus Siguldas Panorāmas ratam un Svētku laukumam.</p>',
                    'en' => '<p>Our holiday houses are located in the center of Sigulda, at Cēsu Street 17, next to the Sigulda Panorama Wheel and Festival Square.</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Vai pie brīvdienu mājām ir pieejama autostāvvieta?',
                    'en' => 'Is there parking available at the holiday houses?',
                ],
                'answer' => [
                    'lv' => '<p>Jā, viesiem ir pieejama bezmaksas privātā autostāvvieta pie naktsmītnes.</p>',
                    'en' => '<p>Yes, free private parking is available for guests at the accommodation.</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Vai varu ierasties ar bērniem?',
                    'en' => 'Can I come with children?',
                ],
                'answer' => [
                    'lv' => '<p>Jā, naktsmītnes ir piemērota ģimenēm. "Black" mājā ir atsevišķa bērnistaba, un tajā ērti var uzturēties divi pieaugušie un līdz diviem bērniem.</p>',
                    'en' => '<p>Yes, the accommodation is suitable for families. The "Black" house has a separate children\'s room and can comfortably accommodate two adults and up to two children.</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Kādi ir reģistrēšanās un izrakstīšanās laiki?',
                    'en' => 'What are the check-in and check-out times?',
                ],
                'answer' => [
                    'lv' => '<p>Reģistrēšanās laiks: no plkst. 15:00 - 18:00</p><p>Izrakstīšanās laiks: līdz plkst. 8:00 - 11:00</p>',
                    'en' => '<p>Check-in time: from 3:00 PM - 6:00 PM</p><p>Check-out time: until 8:00 AM - 11:00 AM</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Vai ir pieejams bezvadu internets?',
                    'en' => 'Is wireless internet available?',
                ],
                'answer' => [
                    'lv' => '<p>Jā, visās telpās ir pieejams bezmaksas Wi-Fi.</p>',
                    'en' => '<p>Yes, free Wi-Fi is available in all rooms.</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Vai brīvdienu mājās ir pieejama virtuve?',
                    'en' => 'Is there a kitchen available in the holiday houses?',
                ],
                'answer' => [
                    'lv' => '<p>Jā, visās brīvdienu mājās ir pilnībā aprīkota virtuve ar plīts virsmu, ledusskapi, trauku mazgājamo mašīnu, kafijas aparātu un visu nepieciešamo maltīšu pagatavošanai.</p>',
                    'en' => '<p>Yes, all holiday houses have a fully equipped kitchen with stovetop, refrigerator, dishwasher, coffee maker and everything needed for meal preparation.</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Vai atļauti ir mājdzīvnieki?',
                    'en' => 'Are pets allowed?',
                ],
                'answer' => [
                    'lv' => '<p>Ar mājdzīvniekiem pašlaik atpūta mūsu dizaina mājās nav atļauta.</p>',
                    'en' => '<p>Currently pets are not allowed in our design houses.</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Kā notiek rezervācija?',
                    'en' => 'How does the reservation process work?',
                ],
                'answer' => [
                    'lv' => '<p>Rezervāciju iespējams ir veikt tiešsaistē caur rezervēšanas platformu booking.com</p>',
                    'en' => '<p>Reservations can be made online through the booking.com reservation platform</p>',
                ],
            ],
            [
                'question' => [
                    'lv' => 'Vai sauna un džakūzī ir iekļauts cenā?',
                    'en' => 'Are the sauna and jacuzzi included in the price?',
                ],
                'answer' => [
                    'lv' => '<p>Nē, sauna un džakuzi nav iekļauti naktsmītnes cenā – tie ir pieejami par papildu samaksu, iepriekš vienojoties par rezervāciju.</p>',
                    'en' => '<p>No, the sauna and jacuzzi are not included in the accommodation price – they are available for an additional fee, with prior reservation.</p>',
                ],
            ],
        ];

        foreach ($faqs as $index => $data) {
            $faq = new Faq([
                'order' => $index,
                'is_active' => true,
            ]);
            $faq->setTranslations('question', $data['question']);
            $faq->setTranslations('answer', $data['answer']);
            $faq->save();
        }
    }
}
