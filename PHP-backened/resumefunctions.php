<?php 

function arraytoText($array) {
    if(count($array) == 1) {
        return $array[0];
    }
    else if(count($array) == 2) {
        return $array[0]." and ".$array[1];
    }
    else if(count($array) > 2) {
        $newarray = [];
        for ($i=0; $i<(count($array)-1);$i++) {
            $newarray[] = $array[$i];
        }
        return implode(', ', $newarray)." and ".$array[count($array)-1];
    }
}

function removeLastElementofArray($array) {
    $newarray = [];
    for($i=0; $i<(count($array)-1);$i++) {
        $newarray[] = $array[$i];
    }
    return $newarray;
}

function fileChecker($allowedfiletypes, $maxsizeinmb, $file, $nameofthefile) {
    $filename = $file['name'];
    $filesize = $file['size'];
    $fileerror = $file['error'];

    $filenameparts = explode('.', $filename);
    $fileext = strtolower(end($filenameparts));

    if(in_array($fileext, $allowedfiletypes) != true) {

        return "The ".$nameofthefile." must be in ".arraytoText($allowedfiletypes)." formats";
    }

    if($fileerror != 0) {
        echo $fileerror;
        return "The ".$nameofthefile." encountered an error during upload";
    }

    if($filesize > $maxsizeinmb*pow(10,6)) {
        return $nameofthefile."size must be smaller than ".$maxsizeinmb." MB";
    }

    return "true";
}

use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

function handleElement($element) {
    $text = '';

    if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
        $content = $element->getText();

        $text .= $content;
    }

    if ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
        $text .= "<br/>";
    }

    if ($element instanceof \PhpOffice\PhpWord\Element\Link) {
        $linkText = $element->getText();
        $text .= $linkText;
    }

    return $text;
}

function extractTextFromTable($table) {
    $tableText = '';
    foreach ($table->getRows() as $row) {
        foreach ($row->getCells() as $cell) {
            foreach ($cell->getElements() as $element) {
                try {
                    $tableText .= $element->getText() . ' ';
                }
                catch (Exception $e) {
                    echo "Error:", $e->getMessage();
                    exit("<br>Problem!");
                }
            }
            $tableText .= "\n";
        }
    }
    return $tableText;
}

function extractTextWithFormatting($filePath, $doctype) {
    $text = '';
    try {
        if($doctype == "MsDoc") {
            $phpWord = IOFactory::load($filePath, "MsDoc");
        }
        else {
            $phpWord = IOFactory::load($filePath);
        }

         foreach ($phpWord->getSections() as $section) {
             $elements = $section->getElements();
             foreach ($elements as $element) {
                 if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                     foreach ($element->getElements() as $childElement) {
                         $text .= handleElement($childElement);
                     }
                     $text .= "\n";
                 } else if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                    $text .= extractTextFromTable($element);
                 }
                 else {
                     $text .= handleElement($element);
                 }
             }
         }

         return nl2br($text);

    } catch (Exception $e) {
        echo 'Error processing file: ',$e->getMessage();
        exit("<br>Problem!");
    }
 }

function pdftoText($filePath) {
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        return nl2br($text);
    }
    catch (Exception $e) {
        echo "Error in parsing pdf file",$e->getMessage();
        exit("<br>Problem!");
    }
}

?>