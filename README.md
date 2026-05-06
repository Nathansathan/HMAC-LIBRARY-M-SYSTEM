const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, HeadingLevel, BorderStyle, WidthType, ShadingType,
  VerticalAlign, LevelFormat
} = require('docx');
const fs = require('fs');
 
const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const noBorder = { style: BorderStyle.NONE, size: 0, color: "FFFFFF" };
const noBorders = { top: noBorder, bottom: noBorder, left: noBorder, right: noBorder };
 
const headerBlue = "1A3C6E";
const accentBlue = "2E75B6";
const lightBlue = "D5E8F0";
const lightGray = "F5F5F5";
const darkText = "1A1A1A";
 
function heading1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    spacing: { before: 300, after: 120 },
    border: { bottom: { style: BorderStyle.SINGLE, size: 6, color: accentBlue, space: 4 } },
    children: [new TextRun({ text, bold: true, size: 28, color: headerBlue, font: "Arial" })]
  });
}
 
function heading2(text) {
  return new Paragraph({
    spacing: { before: 200, after: 80 },
    children: [new TextRun({ text, bold: true, size: 24, color: accentBlue, font: "Arial" })]
  });
}
 
function body(text, options = {}) {
  return new Paragraph({
    spacing: { before: 60, after: 60 },
    children: [new TextRun({ text, size: 22, font: "Arial", color: darkText, ...options })]
  });
}
 
function code(text) {
  return new Paragraph({
    spacing: { before: 40, after: 40 },
    indent: { left: 360 },
    shading: { fill: "F0F0F0", type: ShadingType.CLEAR },
    children: [new TextRun({ text, size: 18, font: "Courier New", color: "333333" })]
  });
}
 
function bullet(text) {
  return new Paragraph({
    numbering: { reference: "bullets", level: 0 },
    spacing: { before: 40, after: 40 },
    children: [new TextRun({ text, size: 22, font: "Arial", color: darkText })]
  });
}
 
function spacer(n = 1) {
  return Array.from({ length: n }, () => new Paragraph({ spacing: { before: 80, after: 80 }, children: [] }));
}
 
function cell(text, opts = {}) {
  const { fill = "FFFFFF", bold = false, textColor = darkText, align = AlignmentType.LEFT, colSpan } = opts;
  const cellProps = {
    borders,
    shading: { fill, type: ShadingType.CLEAR },
    margins: { top: 100, bottom: 100, left: 120, right: 120 },
    verticalAlign: VerticalAlign.CENTER,
    children: [new Paragraph({
      alignment: align,
      children: [new TextRun({ text, bold, size: 20, font: "Arial", color: textColor })]
    })]
  };
  if (colSpan) cellProps.columnSpan = colSpan;
  return new TableCell(cellProps);
}
 
// Members and their roles
const members = [
  { name: "Ron Karlo Lanzanas", role: "Postman & Coding", description: "API development, backend coding, and Postman integration testing" },
  { name: "Lovely Dumalag",     role: "Documentation",   description: "Technical writing, documentation, and project reporting" },
  { name: "Maui Comia",         role: "System Design",   description: "System architecture, database design, and ERD modeling" },
  { name: "Catrissia Par",      role: "UI/UX & Testing", description: "User interface design, frontend layout, and functional testing" },
  { name: "Nathaniel Estabaya", role: "Backend Support", description: "Server-side logic, middleware configuration, and debugging" },
  { name: "Trixjon Umandap",    role: "QA & Testing",    description: "Quality assurance, test case preparation, and bug tracking" },
  { name: "Kyle Anoras",        role: "DevOps & Setup",  description: "Environment setup, deployment configuration, and Git management" },
];
 
const memberTableRows = [
  new TableRow({
    children: [
      cell("Name", { fill: headerBlue, bold: true, textColor: "FFFFFF", align: AlignmentType.CENTER }),
      cell("Role", { fill: headerBlue, bold: true, textColor: "FFFFFF", align: AlignmentType.CENTER }),
      cell("Description", { fill: headerBlue, bold: true, textColor: "FFFFFF", align: AlignmentType.CENTER }),
    ]
  }),
  ...members.map((m, i) =>
    new TableRow({
      children: [
        cell(m.name, { fill: i % 2 === 0 ? "FFFFFF" : lightGray, bold: m.name === "Ron Karlo Lanzanas" }),
        cell(m.role, { fill: i % 2 === 0 ? "FFFFFF" : lightGray, bold: m.name === "Ron Karlo Lanzanas", textColor: accentBlue }),
        cell(m.description, { fill: i % 2 === 0 ? "FFFFFF" : lightGray }),
      ]
    })
  )
];
 
// Endpoints table
const endpoints = [
  ["GET",    "/api/books",       "Retrieve all books"],
  ["POST",   "/api/books",       "Create a new book"],
  ["GET",    "/api/books/{id}",  "Retrieve a specific book"],
  ["PUT",    "/api/books/{id}",  "Update a book"],
  ["DELETE", "/api/books/{id}",  "Delete a book"],
];
 
const endpointColors = { GET: "1D6A1D", POST: "1A3C6E", PUT: "7D4E00", DELETE: "8B0000" };
 
const endpointRows = [
  new TableRow({
    children: [
      cell("Method", { fill: headerBlue, bold: true, textColor: "FFFFFF", align: AlignmentType.CENTER }),
      cell("Endpoint", { fill: headerBlue, bold: true, textColor: "FFFFFF", align: AlignmentType.CENTER }),
      cell("Description", { fill: headerBlue, bold: true, textColor: "FFFFFF", align: AlignmentType.CENTER }),
    ]
  }),
  ...endpoints.map(([method, endpoint, desc]) =>
    new TableRow({
      children: [
        cell(method, { fill: lightBlue, bold: true, textColor: endpointColors[method] || darkText, align: AlignmentType.CENTER }),
        cell(endpoint, { fill: lightGray }),
        cell(desc, { fill: "FFFFFF" }),
      ]
    })
  )
];
 
const doc = new Document({
  numbering: {
    config: [
      {
        reference: "bullets",
        levels: [{ level: 0, format: LevelFormat.BULLET, text: "•", alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
      }
    ]
  },
  styles: {
    default: { document: { run: { font: "Arial", size: 22 } } },
    paragraphStyles: [
      { id: "Heading1", name: "Heading 1", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 28, bold: true, font: "Arial" },
        paragraph: { spacing: { before: 300, after: 120 }, outlineLevel: 0 } },
      { id: "Heading2", name: "Heading 2", basedOn: "Normal", next: "Normal", quickFormat: true,
        run: { size: 24, bold: true, font: "Arial" },
        paragraph: { spacing: { before: 200, after: 80 }, outlineLevel: 1 } },
    ]
  },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 }
      }
    },
    children: [
      // Title block
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 80 },
        children: [new TextRun({ text: "HMAC Library Management System", bold: true, size: 40, font: "Arial", color: headerBlue })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 40 },
        children: [new TextRun({ text: "API Documentation & Project Overview", size: 24, font: "Arial", color: "666666" })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 0 },
        border: { bottom: { style: BorderStyle.SINGLE, size: 12, color: accentBlue, space: 6 } },
        children: [new TextRun({ text: "Built with Laravel · Secured with HMAC-SHA256", size: 20, font: "Arial", color: "888888", italics: true })]
      }),
 
      ...spacer(1),
 
      // About
      heading1("About the Project"),
      body("The HMAC Library Management System is a RESTful API built using the Laravel framework. It manages a library's book inventory and enforces secure access through HMAC-SHA256 (Hash-based Message Authentication Code) request signing, ensuring that all API requests are authenticated and tamper-proof."),
      ...spacer(1),
 
      // Features
      heading1("Features"),
      bullet("HMAC-SHA256 authentication on all API routes"),
      bullet("Full CRUD operations for book management"),
      bullet("Timestamp-based replay attack prevention (±5 minute window)"),
      bullet("Public/secret key pair authentication per user"),
      bullet("Structured JSON responses with descriptive error messages"),
      bullet("Laravel Eloquent ORM with database migrations and factories"),
      ...spacer(1),
 
      // Tech stack
      heading1("Tech Stack"),
      new Table({
        width: { size: 9360, type: WidthType.DXA },
        columnWidths: [3120, 6240],
        rows: [
          new TableRow({ children: [
            cell("Component", { fill: headerBlue, bold: true, textColor: "FFFFFF" }),
            cell("Technology", { fill: headerBlue, bold: true, textColor: "FFFFFF" }),
          ]}),
          new TableRow({ children: [cell("Framework", { fill: "FFFFFF" }), cell("Laravel 11 (PHP)", { fill: "FFFFFF" })]}),
          new TableRow({ children: [cell("Authentication", { fill: lightGray }), cell("HMAC-SHA256 custom middleware", { fill: lightGray })]}),
          new TableRow({ children: [cell("Database", { fill: "FFFFFF" }), cell("MySQL / SQLite", { fill: "FFFFFF" })]}),
          new TableRow({ children: [cell("API Testing", { fill: lightGray }), cell("Postman", { fill: lightGray })]}),
          new TableRow({ children: [cell("Version Control", { fill: "FFFFFF" }), cell("Git & GitHub", { fill: "FFFFFF" })]}),
        ]
      }),
      ...spacer(1),
 
      // HMAC Auth
      heading1("How HMAC Authentication Works"),
      body("Every API request must include the following three HTTP headers:"),
      ...spacer(),
      new Table({
        width: { size: 9360, type: WidthType.DXA },
        columnWidths: [2600, 6760],
        rows: [
          new TableRow({ children: [
            cell("Header", { fill: headerBlue, bold: true, textColor: "FFFFFF" }),
            cell("Purpose", { fill: headerBlue, bold: true, textColor: "FFFFFF" }),
          ]}),
          new TableRow({ children: [cell("X-PUBLIC-KEY", { fill: lightBlue, bold: true }), cell("Identifies the requesting user", { fill: "FFFFFF" })]}),
          new TableRow({ children: [cell("X-TIMESTAMP", { fill: lightGray }), cell("Unix timestamp — must be within ±5 minutes of server time", { fill: lightGray })]}),
          new TableRow({ children: [cell("X-SIGNATURE", { fill: lightBlue, bold: true }), cell("HMAC-SHA256 hash of the request (see formula below)", { fill: "FFFFFF" })]}),
        ]
      }),
      ...spacer(),
      heading2("Signature Formula"),
      code("HMAC-SHA256( METHOD + fullURL + requestBody + timestamp, secret_key )"),
      ...spacer(),
      body("The server independently recomputes the signature using the user's stored secret_key and compares it using a timing-safe hash_equals() check to prevent timing attacks."),
      ...spacer(1),
 
      // API Endpoints
      heading1("API Endpoints"),
      body("All routes are prefixed with /api and protected by the hmac middleware."),
      ...spacer(),
      new Table({
        width: { size: 9360, type: WidthType.DXA },
        columnWidths: [1400, 3200, 4760],
        rows: endpointRows
      }),
      ...spacer(1),
 
      // Setup
      heading1("Installation & Setup"),
      heading2("1. Clone the repository"),
      code("git clone https://github.com/Nathansathan/HMAC-LIBRARY-M-SYSTEM.git"),
      code("cd HMAC-LIBRARY-M-SYSTEM"),
      heading2("2. Install dependencies"),
      code("composer install"),
      heading2("3. Configure environment"),
      code("cp .env.example .env"),
      code("php artisan key:generate"),
      body("Update your .env file with your database credentials."),
      heading2("4. Run migrations"),
      code("php artisan migrate"),
      heading2("5. Seed test users (optional)"),
      code("php artisan db:seed"),
      heading2("6. Serve the application"),
      code("php artisan serve"),
      ...spacer(1),
 
      // Team Members
      heading1("Team Members"),
      body("The following members contributed to the HMAC Library Management System with evenly distributed responsibilities:"),
      ...spacer(),
      new Table({
        width: { size: 9360, type: WidthType.DXA },
        columnWidths: [2600, 2200, 4560],
        rows: memberTableRows
      }),
      ...spacer(1),
 
      // License
      heading1("License"),
      body("This project is developed for academic purposes. All rights reserved by the team members listed above."),
      ...spacer(),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        border: { top: { style: BorderStyle.SINGLE, size: 4, color: "CCCCCC", space: 8 } },
        spacing: { before: 120, after: 0 },
        children: [new TextRun({ text: "HMAC Library Management System — Academic Project", size: 18, font: "Arial", color: "999999", italics: true })]
      }),
    ]
  }]
});
 
Packer.toBuffer(doc).then(buf => {
  fs.writeFileSync('/home/claude/README.docx', buf);
  console.log('Done');
});
