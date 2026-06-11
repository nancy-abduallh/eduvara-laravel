<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\VarkAssessment;
use App\Services\AiApiService;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(private AiApiService $aiService) {}

    public function showVark()
    {
        $locale    = app()->getLocale();
        $questions = $this->getVarkQuestions($locale);
        return view('onboarding.vark', compact('questions'));
    }

    public function submitVark(Request $request)
    {
        $request->validate([
            'answers' => 'required|array|min:8',
        ]);

        $result = $this->aiService->classifyVark($request->answers);

        // 'result' is guaranteed by AiApiService (it falls back to local scoring),
        // but we keep a null-coalescing guard here as a last-resort safety net.
        $dominantStyle = $result['result'] ?? 'visual';

        $assessment = VarkAssessment::create([
            'user_id'          => auth()->id(),
            'answers'          => $request->answers,
            'visual_score'     => $result['visual']      ?? 0,
            'auditory_score'   => $result['auditory']    ?? 0,
            'reading_score'    => $result['reading']     ?? 0,
            'kinesthetic_score'=> $result['kinesthetic'] ?? 0,
            'result'           => $dominantStyle,
            'ai_raw_response'  => $result,
        ]);

        auth()->user()->update([
            'learning_style'       => $dominantStyle,
            'onboarding_completed' => true,
        ]);

        return redirect()->route('onboarding.result');
    }

    public function showResult()
    {
        $assessment = auth()->user()->latestVark;
        if (! $assessment) {
            return redirect()->route('onboarding.vark');
        }
        return view('onboarding.result', compact('assessment'));
    }

    private function getVarkQuestions(string $locale = 'en'): array
    {
        $arQuestions = [
            ['id' => 1, 'text' => 'عندما تحتاج لتعلم شيء جديد، تفضل:',      'options' => ['a' => 'مشاهدة رسم بياني أو فيديو',    'b' => 'الاستماع إلى شرح',       'c' => 'القراءة بالتفصيل',           'd' => 'التجربة العملي']],
            ['id' => 2, 'text' => 'عند إعطاء الاتجاهات، تميل إلى:',          'options' => ['a' => 'رسم خريطة',                    'b' => 'الشرح الشفهي',            'c' => 'الكتابة خطوة بخطوة',         'd' => 'مرافقتهم للمكان']],
            ['id' => 3, 'text' => 'في الفصل، تتعلم بشكل أفضل من:',           'options' => ['a' => 'الرسوم البيانية والشرائح',     'b' => 'المحاضرات والمناقشات',    'c' => 'الكتب المدرسية والملاحظات', 'd' => 'المختبرات والأنشطة']],
            ['id' => 4, 'text' => 'عند حل المشكلات، أنت:',                   'options' => ['a' => 'تتصور الحل',                   'b' => 'تناقشه بصوت عالٍ',        'c' => 'تقرأ المواد ذات الصلة',     'd' => 'تجرب وتختبر']],
            ['id' => 5, 'text' => 'تتذكر الأشياء بشكل أفضل عندما:',          'options' => ['a' => 'ترى صوراً أو رسوماً بيانية',  'b' => 'يخبرك بها شخص',           'c' => 'تكتبها',                    'd' => 'تقوم بها جسدياً']],
            ['id' => 6, 'text' => 'للترفيه، تفضل:',                          'options' => ['a' => 'مشاهدة الأفلام',               'b' => 'الاستماع إلى البودكاست',  'c' => 'قراءة الكتب',               'd' => 'الأنشطة البدنية']],
            ['id' => 7, 'text' => 'عند الدراسة، أنت:',                       'options' => ['a' => 'تستخدم ملاحظات ملونة',         'b' => 'تقرأ بصوت عالٍ',          'c' => 'تكتب ملخصات',              'd' => 'تتجول أثناء التعلم']],
            ['id' => 8, 'text' => 'لفهم شيء معقد، تحتاج إلى:',              'options' => ['a' => 'مخطط انسيابي بصري',            'b' => 'شخص يشرحه لك',            'c' => 'ملاحظات مكتوبة مفصلة',     'd' => 'مثال عملي']],
        ];

        $enQuestions = [
            ['id' => 1, 'text' => 'When you need to learn something new, you prefer to:', 'options' => ['a' => 'Watch a diagram or video',  'b' => 'Listen to an explanation', 'c' => 'Read about it in detail',   'd' => 'Try it hands-on']],
            ['id' => 2, 'text' => 'When giving directions, you tend to:',                 'options' => ['a' => 'Draw a map',                 'b' => 'Explain verbally',         'c' => 'Write step-by-step',        'd' => 'Walk them there']],
            ['id' => 3, 'text' => 'In class, you learn best from:',                      'options' => ['a' => 'Charts and slides',           'b' => 'Lectures and discussions', 'c' => 'Textbooks and notes',       'd' => 'Labs and activities']],
            ['id' => 4, 'text' => 'When solving problems, you:',                         'options' => ['a' => 'Visualize the solution',      'b' => 'Talk it through',          'c' => 'Read related material',     'd' => 'Experiment and test']],
            ['id' => 5, 'text' => 'You remember things best when:',                      'options' => ['a' => 'You see pictures or graphs',  'b' => 'Someone tells you',        'c' => 'You write them down',       'd' => 'You physically do them']],
            ['id' => 6, 'text' => 'For entertainment, you prefer:',                      'options' => ['a' => 'Watching movies',             'b' => 'Listening to podcasts',    'c' => 'Reading books',             'd' => 'Physical activities']],
            ['id' => 7, 'text' => 'When studying, you:',                                 'options' => ['a' => 'Use color-coded notes',       'b' => 'Read aloud to yourself',   'c' => 'Make written summaries',    'd' => 'Walk around while learning']],
            ['id' => 8, 'text' => 'To understand something complex, you need:',          'options' => ['a' => 'A visual flowchart',          'b' => 'Someone to explain it',    'c' => 'Detailed written notes',    'd' => 'A practical example']],
        ];

        return $locale === 'ar' ? $arQuestions : $enQuestions;
    }
}