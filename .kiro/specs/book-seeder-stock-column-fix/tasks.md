# Implementation Plan

## Overview

Fix `BookSeeder` to stop passing `stock` and `shelf_location` to `Book::create()` and instead create individual `BookCopy` records. Single-file change to `database/seeders/BookSeeder.php`.

## Tasks

- [ ] 1. Write bug condition exploration test
  - **Property 1: Bug Condition** - BookSeeder Creates No BookCopy Records
  - **CRITICAL**: This test MUST FAIL on unfixed code — failure confirms the bug exists
  - **DO NOT attempt to fix the test or the code when it fails**
  - **GOAL**: Surface counterexamples that demonstrate the bug exists
  - **Scoped PBT Approach**: Scope the property to the concrete failing case — run the unfixed seeder and assert `BookCopy::count() > 0`
  - Create a test (e.g. `BookSeederTest`) that runs `BookSeeder::run()` against a fresh database with all categories seeded
  - Assert that `BookCopy::count()` equals the total expected copies (80 for the current seed data)
  - Run test on UNFIXED code
  - **EXPECTED OUTCOME**: Test FAILS with `BookCopy::count()` returning 0 (confirms no copies are created)
  - Document the counterexample: "After seeding, BookCopy::count() = 0, expected 80"
  - Mark task complete when test is written, run, and failure is documented
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 2. Write preservation property tests (BEFORE implementing fix)
  - **Property 2: Preservation** - Category Lookup and Skip Logic Unchanged
  - **IMPORTANT**: Follow observation-first methodology — observe behavior on UNFIXED code first
  - Observe: a seed entry with a valid category slug creates one `Book` record (category_id is set correctly)
  - Observe: a seed entry with a missing/unknown category slug creates zero `Book` and zero `BookCopy` records
  - Write property-based test: for any unknown category slug, `Book::count()` and `BookCopy::count()` remain unchanged after attempting to seed that entry
  - Write test: after seeding with all 18 categories present, `Book::count()` equals 18
  - Verify tests PASS on UNFIXED code (this confirms the baseline behavior to preserve)
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [ ] 3. Fix BookSeeder — extract copy fields and create BookCopy records

  - [ ] 3.1 Add `BookCopy` import and implement the fix in `database/seeders/BookSeeder.php`
    - Add `use App\Models\BookCopy;` alongside the existing `use App\Models\Book;` import
    - Inside the `foreach` loop, before calling `Book::create()`, extract `$stock = $book['stock']` and `$shelfLocation = $book['shelf_location']` into local variables, then `unset($book['stock'], $book['shelf_location'])`
    - Call `$createdBook = Book::create($book)` (now without `stock` or `shelf_location`)
    - After creating the book, loop `for ($i = 1; $i <= $stock; $i++)` and call `BookCopy::create()` with: `book_id` → `$createdBook->id`, `shelf_location` → `$shelfLocation`, `condition` → `'baik'`, `is_available` → `true`, `copy_code` → `strtoupper(substr($createdBook->title, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT)`
    - No other changes — category-lookup logic, skip guard, and seed data array remain untouched
    - _Bug_Condition: isBugCondition(entry) where entry contains 'stock' and 'shelf_location' keys passed to Book::create()_
    - _Expected_Behavior: Book::create() receives only fillable fields; BookCopy::create() is called $stock times per book_
    - _Preservation: Category lookup, skip-on-missing-category, and 18-book dataset unchanged_
    - _Requirements: 2.1, 2.2, 2.3, 3.1, 3.2, 3.3_

  - [ ] 3.2 Verify bug condition exploration test now passes
    - **Property 1: Expected Behavior** - BookSeeder Creates Correct BookCopy Records
    - **IMPORTANT**: Re-run the SAME test from task 1 — do NOT write a new test
    - Run `BookSeederTest` against the fixed seeder
    - **EXPECTED OUTCOME**: Test PASSES — `BookCopy::count()` equals 80 (sum of all stock values in seed data)
    - Also assert a sample book (e.g. "Laskar Pelangi") has exactly 5 `BookCopy` records with `shelf_location = 'A-01'`, `condition = 'baik'`, `is_available = true`, and `copy_code` matching `/^LAS-00[1-5]$/`
    - _Requirements: 2.1, 2.2, 2.3_

  - [ ] 3.3 Verify preservation tests still pass
    - **Property 2: Preservation** - Category Lookup and Skip Logic Unchanged
    - **IMPORTANT**: Re-run the SAME tests from task 2 — do NOT write new tests
    - **EXPECTED OUTCOME**: All preservation tests PASS — no regressions in category lookup or skip logic
    - Confirm `Book::count()` equals 18 and all `category_id` values are correctly assigned

- [ ] 4. Checkpoint — Ensure all tests pass
  - Run the full test suite (`php artisan test` or equivalent)
  - Confirm `Book::count() == 18` and `BookCopy::count() == 80` after a fresh seed (`php artisan migrate:fresh --seed`)
  - Confirm `Book::first()->stock` and `Book::first()->available_stock` return correct values via the computed accessors
  - Ensure all tests pass; ask the user if any questions arise

## Task Dependency Graph

```json
{
  "waves": [
    { "wave": 1, "tasks": ["1", "2"] },
    { "wave": 2, "tasks": ["3"] },
    { "wave": 3, "tasks": ["4"] }
  ]
}
```

## Notes

- Total expected `BookCopy` records after seeding: **80** (sum of all `stock` values in the seed data array)
- The `copy_code` format is `strtoupper(substr(title, 0, 3)) . '-' . str_pad(i, 3, '0', STR_PAD_LEFT)` — e.g. `LAS-001` for "Laskar Pelangi" copy 1
- Only `database/seeders/BookSeeder.php` needs to change — no migrations, no model changes
- Run `php artisan migrate:fresh --seed` to validate end-to-end after the fix
