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

    // Margens
    $pdf->SetMargins(15, 15, 15);

    // Adicionar página
    $pdf->AddPage();

    // Cores do sistema
    $corPrimaria = array(37, 99, 235); // #2563eb - blue-600
    $corSecundaria = array(209, 213, 219); // #d1d5db - gray-300
    $corTexto = array(31, 41, 55); // #1f2937 - gray-800
    $corFundo = array(249, 250, 251); // #f9fafb - gray-50
    $corSucesso = array(34, 197, 94); // #22c55e - green-500

    // Cabeçalho com logo e nome do sistema
    $pdf->SetFillColor($corPrimaria[0], $corPrimaria[1], $corPrimaria[2]);
    $pdf->Rect(0, 0, $pdf->getPageWidth(), 40, 'F');
    
    // Nome do sistema
    $pdf->SetFont('helvetica', 'B', 24);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 30, 'MyList', 0, 1, 'C');

    // Subtítulo
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0, 0, 'Sua lista de compras inteligente', 0, 1, 'C');
    
    $pdf->Ln(20);

    // Título da lista
    $pdf->SetTextColor($corTexto[0], $corTexto[1], $corTexto[2]);
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 10, $detalhesLista['nome'], 0, 1, 'L');
    
    // Data de criação
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor($corSecundaria[0], $corSecundaria[1], $corSecundaria[2]);
    $pdf->Cell(0, 10, 'Criada em: ' . date('d/m/Y', strtotime($detalhesLista['data_criacao'])), 0, 1, 'R');
    
    $pdf->Ln(10);

    // Cabeçalho da tabela
    $pdf->SetFillColor($corPrimaria[0], $corPrimaria[1], $corPrimaria[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', 'B', 12);
    
    // Larguras das colunas
    $w = array(100, 30, 30, 30);
    
    $pdf->Cell($w[0], 10, 'Item', 1, 0, 'L', true);
    $pdf->Cell($w[1], 10, 'Qtd.', 1, 0, 'C', true);
    $pdf->Cell($w[2], 10, 'Un.', 1, 0, 'C', true);
    $pdf->Cell($w[3], 10, 'Status', 1, 1, 'C', true);

    // Itens da lista
    $pdf->SetTextColor($corTexto[0], $corTexto[1], $corTexto[2]);
    $pdf->SetFont('helvetica', '', 12);
    $fill = false;
    
    foreach ($itens as $item) {
        // Alternar cor de fundo das linhas
        $pdf->SetFillColor($fill ? 249 : 255, $fill ? 250 : 255, $fill ? 251 : 255);
        
        $pdf->Cell($w[0], 10, $item['nome'], 1, 0, 'L', $fill);
        $pdf->Cell($w[1], 10, $item['quantidade'], 1, 0, 'C', $fill);
        $pdf->Cell($w[2], 10, $item['unidade'], 1, 0, 'C', $fill);
        
        // Status com cor diferente
        $status = $item['comprado'] ? 'Comprado' : 'Pendente';
        if ($item['comprado']) {
            $pdf->SetTextColor($corSucesso[0], $corSucesso[1], $corSucesso[2]);
        }
        $pdf->Cell($w[3], 10, $status, 1, 1, 'C', $fill);
        $pdf->SetTextColor($corTexto[0], $corTexto[1], $corTexto[2]);
        
        $fill = !$fill;
    }

    // Resumo
    $pdf->Ln(10);
    $pdf->SetFillColor($corFundo[0], $corFundo[1], $corFundo[2]);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 12, 'Resumo da Lista', 0, 1, 'L', true);
    
    $pdf->SetFont('helvetica', '', 12);
    $totalItens = count($itens);
    $itensComprados = array_reduce($itens, function($carry, $item) {
        return $carry + ($item['comprado'] ? 1 : 0);
    }, 0);
    
    $pdf->Cell(0, 10, 'Total de itens: ' . $totalItens, 0, 1, 'L', true);
    $pdf->Cell(0, 10, 'Itens comprados: ' . $itensComprados, 0, 1, 'L', true);
    $pdf->Cell(0, 10, 'Itens pendentes: ' . ($totalItens - $itensComprados), 0, 1, 'L', true);

    // Rodapé
    $pdf->SetY(-30);
    $pdf->SetFillColor($corPrimaria[0], $corPrimaria[1], $corPrimaria[2]);
    $pdf->Rect(0, $pdf->GetY(), $pdf->getPageWidth(), 30, 'F');
    
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(0, 20, 'Gerado por MyList em ' . date('d/m/Y H:i'), 0, 0, 'C');

    // Gerar PDF
    $pdf->Output($detalhesLista['nome'] . '.pdf', 'D');
    exit();
} catch (Exception $e) {
    error_log("Erro ao gerar PDF: " . $e->getMessage());
    header('Location: dashboard.php');
    exit();
}
