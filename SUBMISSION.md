# Submission notes

Briefly describe:

- Your approach and any decisions you'd like to explain.
- How to run your solution, if setup has changed, and how you checked it.
- Anything unfinished or any assumptions you made.
- Any AI tools you used and what they helped with, or state none.

## Approach

I first ran the project locally and read the Laravel and Vue code to see what was broken or missing. Then I put the provided starter into git as the first commit, so every change after it can be compared with the original.

I worked in small commits, one bug or one task each, so the history shows how I got to the result:

1. Fix the bugs in the existing table (`fix:` commits).
2. Add the missing actions and the CSV export (`feat:` commits).
3. Once the core worked, clean up and separate concerns (`refactor:` commits).

🫠 I know I added a few extra things on top because I was frustrated with the idea of not finishing it. They're just extras, so they can be evaluated separately from the core work :))

‼️ The third column in the initial table has no heading on purpose, to test the input validation error.

## Bugs fixed

- Typing in a column heading lost focus after every character. The heading text was used as the Vue key, so I gave each column a stable id.
- Renaming a column moved its values under another heading. Values are now stored by column id, so renaming does not touch the rows.
- Empty cells were left behind when a column was renamed. The same change fixed it.
- The export request sent only the rows, without the headings. It now sends the headings and the rows in the order shown on screen.
- Some headings could not be typed: a character was dropped when the text matched another heading (`notes2` became `note2`). Headings are now free text and duplicates are allowed.
- Inputs shrank until they were unusable on a narrow screen. They now keep a minimum width and the table scrolls sideways inside its card, with the remove buttons always visible.

## Features added

- Add a row, add a column, remove a row. Each row has its own id, so removing one does not affect the others.
- Remove a column. The brief asks only for removing rows, but once you can add a column you also need a way to remove one added by mistake. The last column cannot be removed.
- Export: the page sends the table to Laravel, which validates it and sends back `project-register.csv`.
- Every column needs a heading. Laravel rejects an empty one, and the page shows the message under that heading, marks it red and moves focus to it.

## Clean-up

- A reusable `Button` component. The same long button markup was repeated and took a lot of space. It has a `variant` and an `isLoading` state.
- A reusable `Input` component with an optional label and an error state. With an error it sets `aria-invalid`.
- `CSVExportService` holds the CSV work so it can be reused later: `write()` puts a table on any stream, `download()` returns it as a file download.
- Two small front-end helpers, `downloadFile` and `readValidationError`.
- I ran Pint on the project. That commit also reformatted a few starter files; nothing else changed in them.

## Export decisions

- `POST` instead of `PATCH`. The request does not change anything on the server; it sends a table and gets a file back.
- The CSV is written line by line straight to the response, so the file is never built up in memory as a second copy of the table.
- I check the rows in a plain loop instead of Laravel's validator, because the validator copies the data it checks. There is a test for this: 100,000 rows have to export under PHP's default 128 MB limit. With the validator the same test used about 138 MB.
- Laravel trims input and turns empty text into null by default. I switched that off for the export route, so the file matches the table exactly.
- The file starts with a UTF-8 byte order mark, so Excel shows accents and symbols correctly.
- The Export button ignores a second click while an export is running, and a message is shown if the export fails.

## How to run and how I checked it

Setup is unchanged: `composer run setup`, then `composer run dev`, and open http://localhost:8000/table.

- `composer test` runs feature tests for the export endpoint (the CSV output and the validation errors) and unit tests for `CSVExportService`.
- `npm run type-check` passes.
- I also tested each change by hand in the browser.

## Unfinished and assumptions

- Cells that start with `=`, `+`, `-` or `@` are exported as typed. Excel may read them as formulas; I left them so the file matches the table.
- There is no limit on the number of rows or columns; the practical limit is PHP's request size (8 MB by default).
- After removing a row or a column with the keyboard, focus is lost and the user has to tab back to the table.
- A table with one column and an empty cell is written as a blank line, which some CSV readers skip.
- Nothing is saved. A refresh brings back the starting table, as the brief does not ask for saving.
- The `label` option of `Input` is not used on this page yet.

## What I would add in a real project

These are out of scope for this challenge, but this is what I would do next:

- For a more complex version, with Excel files or with import as well as export, I would look at a package like [Laravel Excel](https://laravel-excel.com/), depending on the needs, and run the work on queues with Horizon or something similar.
- I would use more of what Vue 3 offers, like composables, and add tests for the front-end code (there are none here). I would also improve the UI and UX, the error handling and the validation.
- For the interface I would use a UI library like shadcn-vue and an icon library, and add more reusable components, for example for cards and for grid and flex layouts.
- I would add ESLint and Prettier for the front end, next to Pint for PHP, and a CI/CD pipeline that runs the tests, the type check and the formatters on every push. The pipeline could also have an AI step that reviews the changes.
- I would add a `CLAUDE.md`, or a similar set of rules for the AI, and a few agents and skills for the tasks that repeat. I did not create one here because the starter code was broken at the beginning.

## AI tools

I used Claude as a development assistant throughout the exercise, mainly to explore implementation options, discuss trade-offs, and speed up development while keeping the solution aligned with the requirements.

My workflow was iterative:

- I first explored the existing codebase and identified the main issues myself.
- Before implementing each task, I used Claude to discuss possible approaches and trade-offs. I chose the approach based on the requirements, simplicity, and maintainability.
- I reviewed the proposed changes, tested the implementation in the browser, and decided what to keep, adjust, or discard.
- I organized the work into small commits so the implementation history clearly reflects the changes made.

I used specific prompts to keep the solutions focused and avoid unnecessary complexity. For example:

> Help me explore approaches for adding and removing columns, with a minimum of one column. Show me the options before making any changes. Keep it clean and simple.

> Help me design a reusable Button component with variant and isLoading props, following TypeScript conventions. Avoid adding out-of-scope features. Show me the approaches before making changes.

> Help me design a reusable Input component with optional label and error props, including appropriate accessibility attributes. Show me the approaches before making changes.

> I want to extract the CSV streaming logic from the controller into a reusable service. Show me the possible approaches and their trade-offs before making any changes.
