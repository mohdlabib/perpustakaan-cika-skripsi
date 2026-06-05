<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCopy;
use Database\Seeders\BookSeeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * BookSeederTest
 *
 * Tests for the BookSeeder fix (book-seeder-stock-column-fix).
 *
 * Task 1 — Bug Condition Exploration Test (Property 1):
 *   Confirms the bug: after running the unfixed seeder, BookCopy::count() == 0.
 *   This test is EXPECTED TO FAIL on unfixed code.
 *
 * Task 2 — Preservation Property Tests (Property 2):
 *   Confirms category-lookup and skip logic are unchanged.
 *   These tests PASS on both unfixed and fixed code.
 */
class BookSeederTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Task 1 — Bug Condition Exploration (Property 1)
    // Validates: Requirements 1.1, 1.2, 1.3
    // -------------------------------------------------------------------------

    /**
     * Property 1: Bug Condition — BookSeeder Creates BookCopy Records
     *
     * After running BookSeeder with all categories present, BookCopy::count()
     * must equal 83 (the sum of all stock values in the seed data).
     *
     * EXPECTED TO FAIL on unfixed code (confirms bug exists).
     *
     * Validates: Requirements 1.1, 1.2, 1.3
     */
    public function test_book_seeder_creates_expected_book_copy_records(): void
    {
        // Seed categories first (required for BookSeeder to resolve category_id)
        $this->seed(CategorySeeder::class);

        // Run the seeder under test
        $this->seed(BookSeeder::class);

        // Total expected copies = sum of all stock values in seed data = 83
        $this->assertEquals(
            83,
            BookCopy::count(),
            'Expected 83 BookCopy records after seeding, but got ' . BookCopy::count() . '. ' .
            'Counterexample: BookCopy::count() = ' . BookCopy::count() . ', expected 83.'
        );

        // Verify "Laskar Pelangi" has exactly 5 copies with correct attributes
        $laskarPelangi = Book::where('title', 'Laskar Pelangi')->first();
        $this->assertNotNull($laskarPelangi, 'Laskar Pelangi book should exist after seeding.');

        $copies = BookCopy::where('book_id', $laskarPelangi->id)->orderBy('id')->get();
        $this->assertCount(5, $copies, 'Laskar Pelangi should have exactly 5 BookCopy records.');

        foreach ($copies as $copy) {
            $this->assertEquals('A-01', $copy->shelf_location);
            $this->assertEquals('baik', $copy->condition);
            $this->assertTrue((bool) $copy->is_available);
        }

        // Verify copy_codes match LAS-001 through LAS-005
        $copyCodes = $copies->pluck('copy_code')->toArray();
        foreach (['LAS-001', 'LAS-002', 'LAS-003', 'LAS-004', 'LAS-005'] as $expectedCode) {
            $this->assertContains($expectedCode, $copyCodes, "copy_code {$expectedCode} should exist for Laskar Pelangi.");
        }
    }

    // -------------------------------------------------------------------------
    // Task 2 — Preservation Property Tests (Property 2)
    // Validates: Requirements 3.1, 3.2, 3.3, 3.4
    // -------------------------------------------------------------------------

    /**
     * Property 2a: Preservation — Seeding with all categories creates exactly 18 Book records.
     *
     * Validates: Requirements 3.3
     */
    public function test_book_seeder_creates_18_books_when_all_categories_exist(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(BookSeeder::class);

        $this->assertEquals(
            18,
            Book::count(),
            'Expected 18 Book records after seeding with all categories present.'
        );
    }

    /**
     * Property 2b: Preservation — Category ID is correctly assigned for each seeded book.
     *
     * Validates: Requirements 3.1
     */
    public function test_book_seeder_assigns_correct_category_id(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(BookSeeder::class);

        // Every book must have a non-null category_id
        $booksWithoutCategory = Book::whereNull('category_id')->count();
        $this->assertEquals(
            0,
            $booksWithoutCategory,
            'All seeded books should have a valid category_id assigned.'
        );
    }

    /**
     * Property 2c: Preservation — A seed entry with an unknown category slug
     * creates zero Book and zero BookCopy records.
     *
     * Validates: Requirements 3.2
     */
    public function test_book_seeder_skips_entry_with_missing_category(): void
    {
        // Do NOT seed categories — all slugs will be missing
        // Run the seeder; it should skip all entries
        $this->seed(BookSeeder::class);

        $this->assertEquals(
            0,
            Book::count(),
            'No Book records should be created when all category slugs are missing.'
        );

        $this->assertEquals(
            0,
            BookCopy::count(),
            'No BookCopy records should be created when all category slugs are missing.'
        );
    }

    /**
     * Property 2d: Preservation — For any unknown category slug, Book::count()
     * and BookCopy::count() remain unchanged after attempting to seed that entry.
     *
     * Validates: Requirements 3.2
     */
    public function test_book_seeder_does_not_create_records_for_unknown_slugs(): void
    {
        // Seed only a subset of categories (none matching the book slugs)
        // by seeding no categories at all — simulates unknown slugs
        $bookCountBefore = Book::count();
        $copyCountBefore = BookCopy::count();

        $this->seed(BookSeeder::class);

        $this->assertEquals(
            $bookCountBefore,
            Book::count(),
            'Book::count() should not change when no matching categories exist.'
        );

        $this->assertEquals(
            $copyCountBefore,
            BookCopy::count(),
            'BookCopy::count() should not change when no matching categories exist.'
        );
    }
}
