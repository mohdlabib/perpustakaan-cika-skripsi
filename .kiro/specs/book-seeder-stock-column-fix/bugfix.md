# Bugfix Requirements Document

## Introduction

`BookSeeder` fails with `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'stock' in 'field list'` when running `php artisan db:seed`. The seeder was never updated after the schema was refactored from a single `stock` column on the `books` table to a `book_copies` table architecture, where each physical copy is a separate row. As a result, the seeder still attempts to insert `stock` and `shelf_location` directly into `books`, columns that no longer exist.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN `BookSeeder::run()` is executed AND the `books` table no longer has a `stock` column THEN the system throws `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'stock' in 'field list'` and aborts seeding.

1.2 WHEN `BookSeeder::run()` is executed AND the `books` table no longer has a `shelf_location` column THEN the system throws a column-not-found SQL error and aborts seeding.

1.3 WHEN `BookSeeder::run()` completes without error (hypothetically) THEN the system creates no `BookCopy` records, leaving every seeded book with zero copies and zero available stock.

### Expected Behavior (Correct)

2.1 WHEN `BookSeeder::run()` is executed THEN the system SHALL create each `Book` record using only fields present in `Book::$fillable` (`isbn`, `title`, `author`, `edition`, `category_id`, `publisher`, `publication_year`, `publication_place`, `description`, `physical_description`, `classification`, `call_number`, `cover_image`, `custom_fields`), without including `stock` or `shelf_location`.

2.2 WHEN a `Book` record is created by the seeder THEN the system SHALL create one `BookCopy` record per copy (based on the original `stock` value in the seed data) associated with that book.

2.3 WHEN a `BookCopy` record is created by the seeder THEN the system SHALL set `book_id` to the created book's id, `shelf_location` to the original shelf location value from the seed data, `condition` to `'baik'`, `is_available` to `true`, and `copy_code` to an auto-generated value using the first 3 characters of the book title and a sequential number (e.g. `LAS-001`).

### Unchanged Behavior (Regression Prevention)

3.1 WHEN `BookSeeder::run()` is executed THEN the system SHALL CONTINUE TO resolve each book's `category_id` by looking up the `Category` by its slug, exactly as before.

3.2 WHEN `BookSeeder::run()` is executed THEN the system SHALL CONTINUE TO skip creating a book if its corresponding category slug is not found in the database.

3.3 WHEN `BookSeeder::run()` is executed THEN the system SHALL CONTINUE TO seed all 18 books defined in the seed data array (assuming all categories exist).

3.4 WHEN the seeded books are queried through the `Book` model THEN the system SHALL CONTINUE TO return correct `stock` and `available_stock` values via the existing computed attribute accessors (which count `BookCopy` records).
