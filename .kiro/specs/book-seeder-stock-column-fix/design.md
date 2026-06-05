# BookSeeder Stock Column Fix — Bugfix Design

## Overview

`BookSeeder` crashes with `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'stock' in 'field list'` because it still passes `stock` and `shelf_location` directly to `Book::create()`. These columns were removed from the `books` table in migration `2026_04_12_000003_remove_copy_fields_from_books_table.php` when the schema was refactored to a `book_copies` architecture.

The fix is a pure seeder refactor: strip `stock` and `shelf_location` from the `Book::create()` call, then loop N times (where N = the original `stock` value in the seed data) to create individual `BookCopy` records with the correct fields. No migration or model changes are required — the schema and models are already correct.

## Glossary

- **Bug_Condition (C)**: The condition that triggers the bug — `BookSeeder::run()` is called and the seed data array still contains `stock` and/or `shelf_location` keys that are passed directly to `Book::create()`.
- **Property (P)**: The desired behavior — `BookSeeder::run()` completes without error, creating one `Book` record and N `BookCopy` records per seed entry.
- **Preservation**: The existing category-lookup logic, skip-on-missing-category behavior, and the 18-book seed dataset must remain unchanged.
- **`BookSeeder::run()`**: The method in `database/seeders/BookSeeder.php` that iterates over the seed data array and persists books to the database.
- **`Book::$fillable`**: The whitelist of columns accepted by `Book::create()` — does NOT include `stock` or `shelf_location`.
- **`BookCopy::$fillable`**: The whitelist of columns accepted by `BookCopy::create()` — includes `book_id`, `copy_code`, `shelf_location`, `condition`, and `is_available`.
- **`copy_code`**: A human-readable identifier for each physical copy, auto-generated as the first 3 uppercase characters of the book title followed by a zero-padded sequential index (e.g. `LAS-001`, `LAS-002`).

## Bug Details

### Bug Condition

The bug manifests when `BookSeeder::run()` is executed against a database whose `books` table no longer has `stock` or `shelf_location` columns. The seeder passes the raw seed data array (which contains both keys) directly to `Book::create()`, causing MySQL to reject the INSERT statement.

**Formal Specification:**
```
FUNCTION isBugCondition(seederInput)
  INPUT: seederInput — one entry from the $books seed data array
  OUTPUT: boolean

  RETURN ('stock' IN keys(seederInput) OR 'shelf_location' IN keys(seederInput))
         AND seederInput is passed directly to Book::create()
         AND 'stock' NOT IN books table columns
         AND 'shelf_location' NOT IN books table columns
END FUNCTION
```

### Examples

- **Laskar Pelangi** — seed data has `stock: 5`, `shelf_location: 'A-01'`. Current code calls `Book::create($book)` with both keys present → SQL error, seeding aborts.
- **Fisika SMA Kelas XII** — seed data has `stock: 10`, `shelf_location: 'B-01'`. Same failure; no `Book` or `BookCopy` records are created for any book.
- **Ensiklopedia Indonesia** — seed data has `stock: 2`. Even the lowest-stock book triggers the same crash.
- **Edge case — category not found** — if a category slug is missing, the book is skipped entirely; this path is unaffected by the bug and must remain unchanged.

## Expected Behavior

### Preservation Requirements

**Unchanged Behaviors:**
- Category resolution by slug (`Category::where('slug', $categorySlug)->first()`) must continue to work exactly as before.
- Books whose category slug is not found in the database must continue to be silently skipped (no record created).
- All 18 books defined in the seed data array must continue to be attempted (assuming all categories exist).
- The `Book` model's computed `stock` and `available_stock` accessors (which count `BookCopy` rows) must continue to return correct values after seeding — no changes to the model are needed.

**Scope:**
All behavior that does NOT involve writing `stock` or `shelf_location` to the `books` table is completely unaffected by this fix. This includes:
- Category lookup and skip logic
- All other `Book` fields (`title`, `author`, `publisher`, `publication_year`, `description`, `category_id`)
- Any code that reads seeded data after seeding completes

## Hypothesized Root Cause

Based on the bug description and code inspection, the root cause is straightforward:

1. **Stale Seed Data Keys**: The `$books` array in `BookSeeder` still contains `stock` and `shelf_location` keys that were valid before the schema refactor but are no longer columns on the `books` table.

2. **No Key Filtering Before `Book::create()`**: The seeder removes `category` from the array before calling `Book::create()`, but does not remove `stock` or `shelf_location`. Laravel's mass-assignment guard (`$fillable`) would silently ignore unknown keys — however, MySQL still receives them in the raw query because Eloquent builds the INSERT from the array keys, not from `$fillable` alone when using `create()` with an unfiltered array.
   > Note: Eloquent's `create()` does filter through `$fillable`, so the actual failure is that `stock` and `shelf_location` are NOT in `$fillable`, meaning they are silently dropped — but the real issue is that no `BookCopy` records are ever created, leaving every book with zero copies. The SQL error in the bug report may have occurred on an older version of the schema or a different environment; the net effect is the same: seeding is broken.

3. **Missing `BookCopy` Creation Loop**: Even if the `Book::create()` call succeeded silently, the seeder never creates any `BookCopy` records, so every seeded book has zero stock.

## Correctness Properties

Property 1: Bug Condition — BookSeeder Creates BookCopy Records

_For any_ seed data entry where `isBugCondition` holds (i.e., the entry contains `stock` and `shelf_location` keys and the seeder is run against the current schema), the fixed `BookSeeder::run()` SHALL:
- Create exactly one `Book` record using only fields present in `Book::$fillable`
- Create exactly N `BookCopy` records (where N = the `stock` value in the seed entry), each with `book_id` set to the created book's id, `shelf_location` set to the seed entry's `shelf_location`, `condition` set to `'baik'`, `is_available` set to `true`, and `copy_code` set to `strtoupper(substr(title, 0, 3)) . '-' . str_pad(index + 1, 3, '0', STR_PAD_LEFT)`

**Validates: Requirements 2.1, 2.2, 2.3**

Property 2: Preservation — Category Lookup and Skip Logic Unchanged

_For any_ seed data entry where `isBugCondition` does NOT hold (i.e., the category slug is not found, causing the entry to be skipped), the fixed `BookSeeder::run()` SHALL produce exactly the same result as the original seeder — no `Book` or `BookCopy` record is created for that entry.

**Validates: Requirements 3.1, 3.2, 3.3, 3.4**

## Fix Implementation

### Changes Required

**File**: `database/seeders/BookSeeder.php`

**Method**: `run()`

**Specific Changes**:

1. **Extract Copy-Specific Fields Before `Book::create()`**: Before calling `Book::create()`, extract `stock` and `shelf_location` from the `$book` array into local variables, then unset them from the array so they are not passed to `Book::create()`.

2. **Create the Book Without Copy Fields**: Call `Book::create($book)` with only the fields that belong to the `books` table (title, author, publisher, publication_year, description, category_id).

3. **Add `BookCopy` Creation Loop**: After creating the `Book`, loop from `1` to `$stock` (inclusive) and call `BookCopy::create()` for each iteration with:
   - `book_id` → `$book->id` (the newly created book's id)
   - `shelf_location` → the extracted `$shelfLocation` value
   - `condition` → `'baik'`
   - `is_available` → `true`
   - `copy_code` → `strtoupper(substr($book->title, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT)`

4. **Add `BookCopy` Import**: Add `use App\Models\BookCopy;` at the top of the file alongside the existing `use App\Models\Book;` import.

5. **No Other Changes**: The category-lookup logic, the skip-on-missing-category guard, and the seed data array itself remain unchanged.

## Testing Strategy

### Validation Approach

The testing strategy follows a two-phase approach: first, surface counterexamples that demonstrate the bug on the unfixed seeder, then verify the fix creates the correct records and preserves all existing behavior.

### Exploratory Bug Condition Checking

**Goal**: Confirm the root cause by running the unfixed seeder and observing the failure. This validates our hypothesis before writing the fix.

**Test Plan**: Run `php artisan migrate:fresh --seed` (or a targeted seeder test) against the current schema with the unfixed `BookSeeder`. Observe the SQL error or the absence of `BookCopy` records.

**Test Cases**:
1. **SQL Error Test**: Run the unfixed seeder and assert that a `QueryException` is thrown (or that `book_copies` table remains empty). (will fail / error on unfixed code)
2. **Zero Copies Test**: If the seeder completes without error (e.g., due to `$fillable` silently dropping the keys), assert that `BookCopy::count()` equals 0 after seeding. (will fail on unfixed code — 0 copies is wrong)
3. **Book Count Test**: Assert that `Book::count()` equals 18 after seeding. (may pass on unfixed code if `$fillable` silently drops unknown keys, but `BookCopy` count will still be 0)
4. **Edge Case — Missing Category**: Assert that a book with a non-existent category slug produces no `Book` or `BookCopy` record. (should pass on both unfixed and fixed code)

**Expected Counterexamples**:
- `BookCopy::count()` returns 0 after seeding (no copies created)
- Possible causes: `stock` and `shelf_location` never extracted; `BookCopy::create()` loop never written

### Fix Checking

**Goal**: Verify that for all seed entries where the bug condition holds, the fixed seeder creates the correct `Book` and `BookCopy` records.

**Pseudocode:**
```
FOR ALL seedEntry WHERE isBugCondition(seedEntry) DO
  result := runFixedSeeder(seedEntry)
  ASSERT Book::where('title', seedEntry.title)->exists() == true
  ASSERT BookCopy::where('book_id', result.book.id)->count() == seedEntry.stock
  ASSERT BookCopy::where('book_id', result.book.id)->first()->shelf_location == seedEntry.shelf_location
  ASSERT BookCopy::where('book_id', result.book.id)->first()->condition == 'baik'
  ASSERT BookCopy::where('book_id', result.book.id)->first()->is_available == true
  ASSERT BookCopy::where('book_id', result.book.id)->first()->copy_code matches /^[A-Z]{3}-\d{3}$/
END FOR
```

### Preservation Checking

**Goal**: Verify that for all inputs where the bug condition does NOT hold (missing category), the fixed seeder produces the same result as the original — no records created.

**Pseudocode:**
```
FOR ALL seedEntry WHERE NOT isBugCondition(seedEntry) DO
  ASSERT originalSeeder(seedEntry) == fixedSeeder(seedEntry)
  -- i.e., both produce zero Book records and zero BookCopy records for that entry
END FOR
```

**Testing Approach**: Property-based testing is recommended for preservation checking because:
- It can generate many random category-slug combinations to verify the skip logic holds across all inputs
- It catches edge cases (empty slug, null slug, slug with special characters) that manual tests might miss
- It provides strong guarantees that the category-lookup behavior is unchanged

**Test Plan**: Observe the skip behavior on the unfixed seeder first (it already works correctly), then write property-based tests to verify the same behavior holds after the fix.

**Test Cases**:
1. **Missing Category Preservation**: Verify that a seed entry with a non-existent category slug creates no `Book` or `BookCopy` record — both before and after the fix.
2. **Category ID Assignment Preservation**: Verify that `book->category_id` matches `Category::where('slug', slug)->first()->id` for all successfully seeded books.
3. **Seed Data Completeness Preservation**: Verify that exactly 18 `Book` records are created when all 18 category slugs exist in the database.

### Unit Tests

- Test that `Book::create()` is called without `stock` or `shelf_location` keys
- Test that `BookCopy::create()` is called exactly N times per book (where N = stock value)
- Test `copy_code` generation: first 3 chars of title uppercased + `-` + zero-padded index (e.g. `LAS-001` for "Laskar Pelangi" copy 1)
- Test edge case: book with `stock: 2` produces exactly 2 `BookCopy` records with `copy_code` `XXX-001` and `XXX-002`
- Test that a missing category slug results in zero `Book` and zero `BookCopy` records

### Property-Based Tests

- Generate random valid category slugs and verify that every seed entry whose slug matches a category produces exactly `stock` copies
- Generate random missing category slugs and verify that no records are created (preservation of skip logic)
- Generate random `stock` values (1–20) and verify that `BookCopy::count()` equals the sum of all stock values after seeding

### Integration Tests

- Run `php artisan migrate:fresh --seed` end-to-end and assert no exceptions are thrown
- After seeding, assert `Book::count() == 18` and `BookCopy::count() == sum of all stock values` (= 80 for the current seed data)
- After seeding, assert `Book::first()->stock` (computed accessor) returns the correct count via `BookCopy` relationship
- After seeding, assert `Book::first()->available_stock` returns the correct count (all copies available, none borrowed)
