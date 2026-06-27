<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;

if ($argc < 3) {
    echo "Usage: php convert.php <input_markdown_file> <output_docx_file>\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = $argv[2];

if (!file_exists($inputFile)) {
    echo "Error: Input file does not exist.\n";
    exit(1);
}

$mdContent = file_get_contents($inputFile);

$phpWord = new PhpWord();

// Define clean formatting styles
$phpWord->setDefaultFontName('Arial');
$phpWord->setDefaultFontSize(11);

// Add custom Heading Styles
$phpWord->addTitleStyle(1, ['name' => 'Arial', 'size' => 20, 'bold' => true, 'color' => '1B365D'], ['spaceAfter' => 240, 'spaceBefore' => 240]);
$phpWord->addTitleStyle(2, ['name' => 'Arial', 'size' => 14, 'bold' => true, 'color' => '2B4C7E'], ['spaceAfter' => 120, 'spaceBefore' => 180]);
$phpWord->addTitleStyle(3, ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '4A6B82'], ['spaceAfter' => 60, 'spaceBefore' => 120]);

$section = $phpWord->addSection([
    'paperSize' => 'A4',
    'marginLeft' => 1440, // 1 inch
    'marginRight' => 1440,
    'marginTop' => 1440,
    'marginBottom' => 1440,
]);

// Simple parser for parsing core markdown segments
$lines = explode("\n", $mdContent);
$inCodeBlock = false;
$codeLines = [];

foreach ($lines as $line) {
    $trimmed = trim($line);

    // Code Blocks
    if (strpos($trimmed, '```') === 0) {
        if ($inCodeBlock) {
            // End of block
            $codeText = implode("\n", $codeLines);
            $textRun = $section->addText($codeText, ['name' => 'Courier New', 'size' => 9, 'color' => '333333'], ['bgColor' => 'F5F5F5', 'spaceBefore' => 60, 'spaceAfter' => 60]);
            $inCodeBlock = false;
            $codeLines = [];
        } else {
            $inCodeBlock = true;
        }
        continue;
    }

    if ($inCodeBlock) {
        $codeLines[] = $line;
        continue;
    }

    // Horizontal Rule
    if ($trimmed === '---') {
        $section->addPageBreak();
        continue;
    }

    // Headings
    if (strpos($trimmed, '# ') === 0) {
        $section->addTitle(substr($trimmed, 2), 1);
        continue;
    }
    if (strpos($trimmed, '## ') === 0) {
        $section->addTitle(substr($trimmed, 3), 2);
        continue;
    }
    if (strpos($trimmed, '### ') === 0) {
        $section->addTitle(substr($trimmed, 4), 3);
        continue;
    }

    // Bullet Lists
    if (strpos($trimmed, '- ') === 0 || strpos($trimmed, '* ') === 0) {
        $section->addListItem(substr($trimmed, 2), 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_EMPTY]);
        continue;
    }

    // Table markup handler (basic extraction)
    if (strpos($trimmed, '|') === 0) {
        // Skip separator lines
        if (strpos($trimmed, '|---') !== false || strpos($trimmed, '| ---') !== false) {
            continue;
        }
        $cells = array_filter(array_map('trim', explode('|', $trimmed)));
        if (!empty($cells)) {
            $table = isset($currentTable) ? $currentTable : $section->addTable([
                'borderSize' => 6,
                'borderColor' => 'CCCCCC',
                'cellMargin' => 80
            ]);
            $table->addRow();
            foreach ($cells as $cell) {
                $table->addCell(2000)->addText($cell, ['size' => 9.5]);
            }
            $currentTable = $table;
            continue;
        }
    } else {
        // Break table separation
        unset($currentTable);
    }

    // Blank lines
    if ($trimmed === '') {
        $section->addTextBreak(1);
        continue;
    }

    // Default Paragraph text
    // Strip bold markers (**Text**)
    $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $line);
    // Strip links ([Link](url))
    $text = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $text);
    $section->addText($text, ['size' => 11, 'color' => '111111'], ['spaceAfter' => 100]);
}

$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($outputFile);

echo "Successfully converted to Word docx: $outputFile\n";
