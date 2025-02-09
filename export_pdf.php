<?php
session_start();
require_once 'config/database.php';
require_once 'models/Lista.php';
require_once 'vendor/autoload.php';

// Verificar autenticação
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID da lista foi fornecido
if (!isset($_GET['lista_id']) || empty($_GET['lista_id'])) {
    header('Location: dashboard.php');
    exit();
}

try {
    // Inicializar lista
    $lista = new Lista();
    $lista->id = $_GET['lista_id'];
    $lista->usuario_id = $_SESSION['user_id'];

    // Buscar detalhes da lista
    $detalhesLista = $lista->obterDetalhesLista();
    if (!$detalhesLista || $detalhesLista['usuario_id'] != $_SESSION['user_id']) {
        throw new Exception("Lista não encontrada ou sem permissão");
    }

    // Buscar itens da lista
    $itens = $lista->buscarItens();

    // Criar PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Configurar informações do documento
    $pdf->SetCreator('MyList');
    $pdf->SetAuthor('MyList');
    $pdf->SetTitle($detalhesLista['nome']);

    // Remover cabeçalho e rodapé padrão
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Adicionar página
    $pdf->AddPage();

    // Configurar fonte
    $pdf->SetFont('helvetica', '', 12);

    // Título da lista
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, $detalhesLista['nome'], 0, 1, 'C');
    $pdf->Ln(5);

    // Data de criação
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 10, 'Criada em: ' . date('d/m/Y', strtotime($detalhesLista['data_criacao'])), 0, 1, 'R');
    $pdf->Ln(5);

    // Cabeçalho da tabela
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(100, 10, 'Item', 1, 0, 'L');
    $pdf->Cell(30, 10, 'Quantidade', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Unidade', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Status', 1, 1, 'C');

    // Itens da lista
    $pdf->SetFont('helvetica', '', 12);
    foreach ($itens as $item) {
        $pdf->Cell(100, 10, $item['nome'], 1, 0, 'L');
        $pdf->Cell(30, 10, $item['quantidade'], 1, 0, 'C');
        $pdf->Cell(30, 10, $item['unidade'], 1, 0, 'C');
        $pdf->Cell(30, 10, $item['comprado'] ? 'Comprado' : 'Pendente', 1, 1, 'C');
    }

    // Resumo
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Resumo:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Total de itens: ' . count($itens), 0, 1, 'L');
    $pdf->Cell(0, 10, 'Itens comprados: ' . array_reduce($itens, function($carry, $item) {
        return $carry + ($item['comprado'] ? 1 : 0);
    }, 0), 0, 1, 'L');

    // Gerar PDF
    $pdf->Output($detalhesLista['nome'] . '.pdf', 'D');
    exit();
} catch (Exception $e) {
    error_log("Erro ao gerar PDF: " . $e->getMessage());
    header('Location: dashboard.php');
    exit();
}
