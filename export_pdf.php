<?php
require_once 'config/database.php';
require_once 'models/Lista.php';
require_once 'vendor/autoload.php';

session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['lista_id'])) {
    header('Location: dashboard.php');
    exit();
}

$lista = new Lista($conn);
$lista_id = $_GET['lista_id'];
$itens = $lista->getItensByListaId($lista_id);
$lista_info = $lista->getListaById($lista_id);

if (!$lista_info || $lista_info['usuario_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit();
}

// Criar nova instância do TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar informações do documento
$pdf->SetCreator('MyList');
$pdf->SetAuthor('MyList App');
$pdf->SetTitle($lista_info['nome']);

// Remover cabeçalho e rodapé padrão
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Adicionar página
$pdf->AddPage();

// Configurar fonte
$pdf->SetFont('helvetica', 'B', 20);

// Título da lista
$pdf->Cell(0, 15, $lista_info['nome'], 0, true, 'C');
$pdf->Ln(10);

// Data de exportação
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'Exportado em: ' . date('d/m/Y H:i'), 0, true, 'R');
$pdf->Ln(5);

// Cabeçalho da tabela
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(15, 10, 'Item', 1, 0, 'C', true);
$pdf->Cell(100, 10, 'Nome', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Quantidade', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Status', 1, 1, 'C', true);

// Conteúdo da tabela
$pdf->SetFont('helvetica', '', 12);
$counter = 1;
foreach ($itens as $item) {
    $status = $item['comprado'] ? 'Comprado' : 'Pendente';
    $pdf->Cell(15, 10, $counter, 1, 0, 'C');
    $pdf->Cell(100, 10, $item['nome'], 1, 0, 'L');
    $pdf->Cell(35, 10, $item['quantidade'], 1, 0, 'C');
    $pdf->Cell(40, 10, $status, 1, 1, 'C');
    $counter++;
}

// Informações adicionais
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 10, 'Total de itens: ' . count($itens), 0, true, 'L');
$comprados = array_filter($itens, function($item) { return $item['comprado'] == 1; });
$pdf->Cell(0, 10, 'Itens comprados: ' . count($comprados), 0, true, 'L');
$pdf->Cell(0, 10, 'Itens pendentes: ' . (count($itens) - count($comprados)), 0, true, 'L');

// Gerar PDF
$pdf->Output($lista_info['nome'] . '_' . date('Y-m-d') . '.pdf', 'D');
