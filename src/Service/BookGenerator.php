<?php

namespace App\Service;

use Faker\Factory;
use Faker\Generator;

class BookGenerator
{
    private Generator $faker;

    public function generateBooks(string $locale, int $seed, int $page, float $avgLikes, float $avgReviews): array
    {
        $fullSeed = $seed + $page;
        mt_srand($fullSeed);

        $this->faker = Factory::create($locale);
        $this->titleFaker = Factory::create($locale);

        $this->faker->seed($fullSeed);
        $this->titleFaker->seed($fullSeed);

        $books = [];
        $count = ($page === 1) ? 20 : 10;

        for ($i = 0; $i < $count; $i++) {
            $index = ($page - 1) * 10 + $i + 1;

            $likes = $this->floatCount($avgLikes);
            $reviews = $this->generateReviews($avgReviews);

            $company = preg_replace('/[,()]/', '', $this->faker->company());
            $year = $this->faker->year();
            $publisher = trim($company) . ' (' . $year . ')';

            $authors = [$this->faker->name()];
            if (mt_rand(0, 10) > 7) {
                $authors[] = $this->faker->name();
            }

            $title = $this->generateBookTitle($locale);

            $books[] = [
                'index' => $index,
                'isbn' => $this->generateFormattedIsbn(),
                'title' => $title,
                'authors' => $authors,
                'publisher' => $publisher,
                'likes' => $likes,
                'reviews' => $reviews,
            ];
        }

        return $books;
    }

    private function floatCount(float $avg): int
    {
        $whole = floor($avg);
        $fraction = $avg - $whole;
        return $whole + ((mt_rand(0, 1000) / 1000.0) < $fraction ? 1 : 0);
    }

    private function generateReviews(float $avg): array
    {
        $reviews = [];
        $count = $this->floatCount($avg);

        for ($i = 0; $i < $count; $i++) {
            $reviews[] = [
                'author' => $this->faker->name(),
                'text' => $this->faker->realText(100),
            ];
        }

        return $reviews;
    }

    private function generateFormattedIsbn(): string
    {
        $raw = preg_replace('/[^0-9]/', '', $this->faker->isbn13());
        return  substr($raw, 0, 3) . '-' .
                substr($raw, 3, 1) . '-' .
                substr($raw, 4, 2) . '-' .
                substr($raw, 6, 6) . '-' .
                substr($raw, 12, 1);
    }

    private function generateBookTitle(string $locale): string
    {

        $text = $this->titleFaker->realText(40);
        $text = preg_replace('/[.,;:!?]$/u', '', $text);
        $title = mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');

        return $title;
    }

}