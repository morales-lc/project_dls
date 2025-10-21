<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MidesDocument;
use App\Models\MidesCategory;
use Illuminate\Support\Str;

class MidesDocumentSeeder extends Seeder
{
    public function run(): void
    {
        // Build a larger, diverse dataset programmatically
        $topics = [
            'Assessment', 'Evaluation', 'Analysis', 'Impact', 'Adoption',
            'Effectiveness', 'Design', 'Implementation', 'Perception', 'Readiness',
            'Innovation', 'Outcomes', 'Satisfaction', 'Engagement', 'Trends'
        ];
        $lastNames = ['Santos', 'Reyes', 'Garcia', 'Dela Cruz', 'Torres', 'Villanueva', 'Lopez', 'Gonzales', 'Ramos', 'Navarro'];
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Mark', 'Lea', 'Eliza', 'Paolo', 'Carla', 'Dino'];

        $index = 0;
        $makeAuthor = function () use (&$index, $lastNames, $firstNames) {
            $ln = $lastNames[$index % count($lastNames)];
            $fn = $firstNames[$index % count($firstNames)];
            $mi = chr(65 + ($index % 26)); // A-Z middle initial
            $index++;
            return "$ln, $fn $mi.";
        };

        // Helper to seed docs for a category group
        $seedCategoryDocs = function (string $type, string $categoryName, ?string $program, int $count, string $basePath) use ($topics, &$makeAuthor) {
            $categoryId = $this->findCategoryId($type, $categoryName);
            for ($i = 0; $i < $count; $i++) {
                $topic = $topics[$i % count($topics)];
                $year = 2017 + (($i + strlen($categoryName)) % 9); // 2017-2025
                $title = $this->buildTitle($categoryName, $topic, $year);
                $author = $makeAuthor();
                $path = $this->buildPdfPath($basePath, $categoryName, $title);

                MidesDocument::updateOrCreate(
                    ['title' => $title, 'year' => $year],
                    [
                        'type' => $type,
                        'category' => $categoryName,
                        'program' => $program,
                        'mides_category_id' => $categoryId,
                        'author' => $author,
                        'year' => $year,
                        'title' => $title,
                        'pdf_path' => $path,
                    ]
                );
            }
        };

        // Graduate Theses: pick multiple categories
        $graduateCategories = [
            'MAED-Childhood Education',
            'MAED-Elementary Education',
            'MAED-Educational Management',
            'Master in Library and Information Science',
            'Master in Business Administration',
            'Master of Science in Hospitality Management',
            'Master of Arts in Home Economics',
            'Master of Science in Social Work',
        ];
        foreach ($graduateCategories as $cat) {
            $seedCategoryDocs('Graduate Theses', $cat, null, 3, 'mides/graduate');
        }

        // Undergraduate Baby Theses: broader set
        $undergradCategories = [
            'Information Technology', 'Psychology', 'Nursing', 'Pharmacy', 'Accountancy',
            'Business Administration', 'Mass Communication', 'Library and Information Science',
            'Elementary Education', 'Secondary Education', 'English', 'Filipino'
        ];
        foreach ($undergradCategories as $cat) {
            $seedCategoryDocs('Undergraduate Baby Theses', $cat, $cat, 2, 'mides/undergrad');
        }

        // Senior High School Research Paper: strands
        $seniorHighMap = [
            'Accountancy, Business and Management (ABM)' => 'ABM',
            'Humanities and Social Sciences Strand (HUMSS)' => 'HUMSS',
            'Science, Technology, Engineering and Mathematics (STEM)' => 'STEM',
            'Technical-Vocational-Livelihood (TVL)' => 'TVL',
            'Information Computer Technology' => 'ICT',
            'Culinary Arts' => 'Culinary',
        ];
        foreach ($seniorHighMap as $cat => $program) {
            $seedCategoryDocs('Senior High School Research Paper', $cat, $program, 2, 'mides/seniorhigh');
        }

        // Faculty/Theses/Dissertations: not tied to category
        for ($i = 0; $i < 6; $i++) {
            $topic = $topics[$i % count($topics)];
            $year = 2018 + ($i % 7); // 2018-2024
            $title = "Faculty Research on $topic ($year)";
            $author = $makeAuthor();
            $path = $this->buildPdfPath('mides/faculty', 'faculty', $title);

            MidesDocument::updateOrCreate(
                ['title' => $title, 'year' => $year],
                [
                    'type' => 'Faculty/Theses/Dissertations',
                    'category' => null,
                    'program' => null,
                    'mides_category_id' => null,
                    'author' => $author,
                    'year' => $year,
                    'title' => $title,
                    'pdf_path' => $path,
                ]
            );
        }
    }

    private function findCategoryId(string $type, ?string $name): ?int
    {
        if (!$name) {
            return null;
        }
        $cat = MidesCategory::where('type', $type)
            ->where('name', $name)
            ->first();

        return $cat?->id;
    }

    private function buildTitle(string $categoryName, string $topic, int $year): string
    {
        return "$categoryName: $topic Study ($year)";
    }

    private function buildPdfPath(string $basePath, string $categoryName, string $title): string
    {
        $catSlug = Str::slug($categoryName ?: 'general');
        $titleSlug = Str::slug($title);
        return rtrim($basePath, '/') . "/$catSlug/$titleSlug.pdf";
    }
}
