<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MidesDocument;
use App\Models\MidesCategory;
use Illuminate\Support\Str;

class MidesGraduateChildhoodEducationSeeder extends Seeder
{
    public function run(): void
    {
        $type = 'Graduate Theses';
        $categoryName = 'MAED-Childhood Education';
        $category = MidesCategory::where('type', $type)
            ->where('name', $categoryName)
            ->first();

        $categoryId = $category?->id;

        $topics = [
            'Play-Based Learning', 'Early Numeracy Skills', 'Literacy Development', 'Socio-Emotional Learning',
            'Parental Involvement', 'Inclusive Education', 'Digital Tools in ECE', 'Teacher Professional Development',
            'Classroom Management', 'Attention and Engagement', 'Assessment Practices', 'Language Acquisition',
            'Arts Integration', 'STEM for Early Learners', 'Outdoor Learning', 'Culturally Responsive Teaching',
            'Health and Nutrition', 'Motor Skills Development', 'Collaborative Learning', 'Inquiry-Based Learning'
        ];

        for ($i = 0; $i < 20; $i++) {
            $year = 2010 + ($i % 16); // 2010-2025
            $topic = $topics[$i % count($topics)];
            $title = "$categoryName: $topic Study ($year)";
            $author = $this->fakeAuthor($i);
            $pdfPath = $this->pdfPath('mides/graduate', $categoryName, $title);

            MidesDocument::updateOrCreate(
                ['title' => $title, 'year' => $year],
                [
                    'type' => $type,
                    'category' => $categoryName,
                    'program' => null,
                    'mides_category_id' => $categoryId,
                    'author' => $author,
                    'year' => $year,
                    'title' => $title,
                    'pdf_path' => $pdfPath,
                ]
            );
        }
    }

    private function fakeAuthor(int $seed): string
    {
        $last = ['Santos', 'Reyes', 'Garcia', 'Dela Cruz', 'Torres', 'Villanueva', 'Lopez', 'Ramos', 'Navarro', 'Castro'];
        $first = ['Juan', 'Maria', 'Jose', 'Ana', 'Mark', 'Lea', 'Eliza', 'Paolo', 'Carla', 'Dino'];
        $ln = $last[$seed % count($last)];
        $fn = $first[$seed % count($first)];
        $mi = chr(65 + ($seed % 26));
        return "$ln, $fn $mi.";
    }

    private function pdfPath(string $base, string $categoryName, string $title): string
    {
        $catSlug = Str::slug($categoryName);
        $titleSlug = Str::slug($title);
        return rtrim($base, '/') . "/$catSlug/$titleSlug.pdf";
    }
}
