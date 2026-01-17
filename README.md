# Insurance Broker Web Application

A static multi-page web application generated from Excel design specifications. This application provides a complete UI for managing insurance broker data with forms, tables, search, pagination, and export functionality.

## Features

- **Multi-page Navigation**: Each Excel sheet becomes a dedicated page
- **Dynamic Forms**: Auto-generated forms based on field definitions
- **Data Tables**: Sortable, searchable tables with pagination
- **Export to CSV**: Export filtered data with column selection
- **Responsive Design**: Works on desktop and mobile devices
- **Dark Theme**: Modern UI with orange accent colors
- **Column Toggle**: Show/hide table columns dynamically

## Project Structure

```
.
├── index.html              # Main dashboard page
├── pages/                  # Generated pages (one per Excel sheet)
│   ├── fields&module.html
│   ├── system-db.html
│   └── ...
├── styles/
│   └── styles.css         # Main stylesheet
├── scripts/
│   └── script.js          # Main JavaScript functionality
├── data/
│   └── design.json        # Parsed Excel metadata (auto-generated)
├── tools/
│   ├── parse_excel.py     # Excel parser script
│   └── generate_pages.py # Page generator script
├── assets/
│   ├── logo.svg
│   └── icons/
└── dist/
    └── index.html         # Single-file build (all-in-one)
```

## Getting Started

### Prerequisites

- Python 3.7+ with pandas and openpyxl installed:
  ```bash
  pip install pandas openpyxl
  ```

### Step 1: Parse Excel File

Run the parsing script to generate `data/design.json`:

```bash
python tools/parse_excel.py
```

This script:
- Reads the Excel file: `IDB Oct 25 (2).xlsx`
- Extracts field definitions from each sheet
- Detects field types (text, date, numeric, boolean, lookup)
- Generates summary statistics
- Outputs JSON metadata to `data/design.json`

### Step 2: Generate Pages (Optional)

If you need to regenerate pages after modifying the design:

```bash
python tools/generate_pages.py
```

### Step 3: Open the Application

Simply open `index.html` in a web browser:

```bash
# On Windows
start index.html

# On macOS
open index.html

# On Linux
xdg-open index.html
```

Or use a local web server (recommended):

```bash
# Python 3
python -m http.server 8000

# Then open: http://localhost:8000
```

## Usage

### Navigation

- Use the **sidebar** to navigate between pages
- Each page corresponds to an Excel sheet
- The **Dashboard** (index.html) provides an overview

### Adding Data

1. Scroll to the **form section** on any page
2. Fill in the required fields (marked with *)
3. Click **Save** to add the record to the table

### Viewing Data

- **Search**: Use the search box to filter table rows
- **Sort**: Click column headers to sort ascending/descending
- **Pagination**: Navigate through pages using pagination controls
- **Column Toggle**: Click "Columns" button to show/hide columns
- **Export**: Click "Export CSV" to download filtered data

### Field Types

The application automatically detects and renders:

- **Text**: Standard text input
- **Date**: Date picker input
- **Numeric**: Number input with decimal support
- **Boolean**: Checkbox
- **Lookup**: Dropdown select with sample values
- **Email**: Email input with validation
- **Phone**: Tel input

## Customization

### Styling

Edit `styles/styles.css` to customize:
- Colors (CSS variables in `:root`)
- Layout dimensions
- Typography
- Component styles

### Functionality

Edit `scripts/script.js` to customize:
- Table behavior
- Form validation
- Export format
- Data generation

### Data Source

Replace `data/design.json` with your own data structure, or modify `tools/parse_excel.py` to parse different Excel formats.

## Single-File Build

A single-file version is available in `dist/index.html` that includes all CSS and JavaScript inline. This is useful for:
- Quick sharing
- Offline use
- Deployment without a web server

To regenerate the single-file build, run:

```bash
python tools/build_single_file.py
```

## Technical Details

### Design Language

- **Primary Color**: Orange (#ff7f00)
- **Dark Header**: #2c3e50
- **Background**: Light gray (#ecf0f1)
- **Cards**: White with subtle shadow
- **Typography**: Segoe UI / Roboto fallback

### Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

### Data Format

The `design.json` structure:

```json
{
  "sheets": [
    {
      "name": "Sheet Name",
      "page": "sheet-name.html",
      "fields": [
        {
          "name": "Field Name",
          "type": "text|Date|Numeric|Boolean|Lookup",
          "uiType": "text|date|number|checkbox|select",
          "required": true|false,
          "sample": ["value1", "value2"]
        }
      ],
      "summary": {
        "Tasks": 13,
        "Claims": 11
      }
    }
  ]
}
```

## Troubleshooting

### Excel File Not Found

Ensure `IDB Oct 25 (2).xlsx` is in the project root directory.

### Pages Not Loading

- Check browser console for errors
- Ensure `data/design.json` exists and is valid JSON
- Verify file paths are correct (use relative paths)

### Styling Issues

- Clear browser cache
- Check CSS file path in HTML
- Verify CSS variables are supported

### JavaScript Errors

- Open browser developer tools (F12)
- Check Console tab for errors
- Ensure `scripts/script.js` loads correctly

## License

This project is generated from design specifications and is intended for internal use.

## Support

For issues or questions:
1. Check the browser console for errors
2. Verify all files are in place
3. Ensure Python dependencies are installed
4. Review the Excel file structure matches expected format
