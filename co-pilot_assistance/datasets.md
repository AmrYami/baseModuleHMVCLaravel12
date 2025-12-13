Datasets and Option Resolution
==============================

Manual Options
- `form_fields.options` stores an array of options: { label, value, default?, disabled?, meta? }.

Dataset Options
- `form_fields.ui.source = { type: 'dataset', key: '<dataset_key>' }`.
- If the dataset’s `meta.source` starts with `lookup:`, options are read dynamically via `LookupOptions::options(<lookup_key>)`.
- Otherwise, options come from `hr_dataset_items` for the given dataset, ordered by `position` and filtered by `disabled = false` (when using `activeItems`).

Lookup Datasets
- Useful for countries, specialties, years_of_experience, etc. The preview in admin uses `LookupOptions::options()` to show sample items.

Bulk Add Presets
- Admin can add multiple dataset‑backed fields at once via `forms/{form}/fields/bulk`.
- Validates only selected fields (`include = 1`), suggests label/key based on dataset.

