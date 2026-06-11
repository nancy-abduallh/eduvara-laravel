<?php

/**
 * VARK Answer Map
 * Maps each question ID + answer key to a VARK dimension.
 * Used for local scoring fallback when AI backend is unavailable.
 *
 * Dimensions: visual | auditory | reading | kinesthetic
 */
return [
    'answer_map' => [
        // Q1: When you need to learn something new, you prefer to:
        1 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q2: When giving directions
        2 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q3: In class you learn best from
        3 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q4: When solving problems
        4 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q5: You remember things best when
        5 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q6: For entertainment
        6 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q7: When studying
        7 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q8: To understand something complex
        8 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q9: When assembling furniture
        9 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q10: Your ideal study session
        10 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q11: When reviewing for exams
        11 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q12: When receiving feedback
        12 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q13: You find it easiest to explain things by
        13 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q14: In meetings, you pay attention to
        14 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q15: When learning a language
        15 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
        // Q16: After a learning session you feel confident when
        16 => ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'],
    ],

    'styles' => [
        'visual'      => ['icon' => '👁️', 'color' => '#7C3AED', 'description' => 'Visual learners benefit from diagrams, charts, and animations.'],
        'auditory'    => ['icon' => '👂', 'color' => '#F59E0B', 'description' => 'Auditory learners benefit from narration and verbal explanations.'],
        'reading'     => ['icon' => '📖', 'color' => '#10B981', 'description' => 'Reading/Writing learners benefit from structured text and notes.'],
        'kinesthetic' => ['icon' => '🤲', 'color' => '#EC4899', 'description' => 'Kinesthetic learners benefit from examples and hands-on scenarios.'],
    ],

    'pass_score' => 70, // Quiz pass threshold (%)
];
