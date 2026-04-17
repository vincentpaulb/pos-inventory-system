<?php
$pdfCurrency = static function ($amount): string {
    return 'PHP ' . number_format((float) $amount, 2);
};

$quotationHeaderImageUrl = base_url('public/images/header.png');
$quotationSubjectParagraphs = [
    'We respectfully submit this quotation for your kind review and approval. The prices and corresponding service charges outlined herein are carefully based on the requested items, as well as the applicable repair option determined at the time of issuance.',
    'Please note that all costs provided reflect current assessments and are subject to change should there be any modifications to the scope of work, materials required, or prevailing conditions at the time of service. Should you require any clarification or further details regarding this quotation, we would be pleased to assist you.',
];

$quotationDefaultNotes = 'The prices quoted herein are subject to stock availability and may change without prior notice. All pricing is based on current market conditions at the time of issuance.

Any additional work, services, or materials not expressly included within the stated scope shall be subject to separate evaluation, approval, and quotation. No such work shall proceed without prior written authorization.';

$quotationPdfPayload = json_encode([
    'quoteNo' => (string) $quotation['quote_no'],
    'createdAt' => (string) format_datetime($quotation['created_at']),
    'preparedBy' => (string) ($quotation['prepared_by'] ?: 'System'),
    'validUntil' => (string) ($quotation['valid_until'] ? format_date($quotation['valid_until']) : 'Until further notice'),
    'customerName' => (string) $quotation['customer_name'],
    'customerContact' => (string) ($quotation['customer_contact'] ?: 'Not provided'),
    'customerAddress' => (string) ($quotation['customer_address'] ?: 'Not provided'),
    'serviceOption' => (string) ($quotation['service_option'] === 'with_service_repair' ? 'With Service Repair' : 'Without Service Repair'),
    'serviceDescription' => (string) ($quotation['service_description'] ?: 'No service work included in this quotation.'),
    'subjectParagraphs' => $quotationSubjectParagraphs,
    'termsLabel' => 'Terms and Conditions',
    'notes' => (string) ($quotation['notes'] ?: $quotationDefaultNotes),
    'subtotalAmount' => $pdfCurrency($quotation['subtotal_amount']),
    'serviceFee' => $pdfCurrency($quotation['service_fee']),
    'totalAmount' => $pdfCurrency($quotation['total_amount']),
    'headerImageUrl' => (string) $quotationHeaderImageUrl,
    'companyName' => (string) APP_NAME,
    'companySubtitle' => 'Heavy Equipment Parts Trading',
    'companyCaption' => 'Formal Sales Quotation',
    'items' => array_map(function ($item, $index) use ($pdfCurrency) {
        return [
            'index' => (string) ($index + 1),
            'product' => (string) ($item['product_name'] ?: 'Deleted Product'),
            'quantity' => (string) ((int) $item['quantity']),
            'unitType' => (string) ($item['unit_type'] ?: 'PC'),
            'unitPrice' => $pdfCurrency($item['unit_price']),
            'subtotal' => $pdfCurrency($item['subtotal']),
        ];
    }, $items, array_keys($items)),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>

<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Quotation Details</h1>
        <p class="page-header-desc">Review, print, or share the quotation with your customer.</p>
    </div>
    <div class="page-header-actions">
        <button type="button" class="btn btn-primary" id="downloadQuotationPdfBtn">
            <i class="fas fa-file-pdf"></i> Download PDF
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Quotation
        </button>
        <a class="btn btn-outline-secondary" href="<?= e(base_url('quotations')) ?>">
            <i class="fas fa-arrow-left"></i> Back to Quotations
        </a>
    </div>
</div>

<div class="quotation-print-card">
    <div id="printArea" class="quotation-paper">
        <div class="quotation-paper-header">
            <div class="quotation-paper-company">
                <img
                    src="<?= e($quotationHeaderImageUrl) ?>"
                    alt="<?= e(APP_NAME) ?> header"
                    class="quotation-paper-company-image"
                >
            </div>
            <div class="quotation-paper-meta">
                <div class="quotation-paper-meta-row"><span>Quotation No.</span><strong><?= e($quotation['quote_no']) ?></strong></div>
                <div class="quotation-paper-meta-row"><span>Date Issued</span><strong><?= e(format_datetime($quotation['created_at'])) ?></strong></div>
                <div class="quotation-paper-meta-row"><span>Validity</span><strong><?= e($quotation['valid_until'] ? format_date($quotation['valid_until']) : 'Until further notice') ?></strong></div>
            </div>
        </div>

        <div class="quotation-paper-subject">
            <div class="quotation-paper-subject-label">Subject</div>
            <p class="quotation-paper-subject-copy mb-0">
                <?= e($quotationSubjectParagraphs[0]) ?>
            </p><br>
            <p class="quotation-paper-subject-copy mb-0">
                <?= e($quotationSubjectParagraphs[1]) ?>
            </p>
        </div>

        <div class="quotation-paper-section-grid">
            <div class="quotation-paper-panel">
                <div class="quotation-paper-label">Customer Information: </div>
                <div class="quotation-paper-customer"><?= e($quotation['customer_name']) ?></div>
                <div class="quotation-paper-field"><span>Contact No.: <strong><?= e($quotation['customer_contact'] ?: 'Not provided') ?></strong></span></div>
                <div class="quotation-paper-field"><span>Address: <strong><?= e($quotation['customer_address'] ?: 'Not provided') ?></strong></span></div>
                
            </div>
            <div class="quotation-paper-panel">
                <div class="quotation-paper-label">Quotation Details</div>
                <div class="quotation-paper-field"><span>Service Option: <strong><?= e($quotation['service_option'] === 'with_service_repair' ? 'With Service Repair' : 'Without Service Repair') ?></strong></span></div>
                <div class="quotation-paper-field"><span>Service Description: <strong><?= e($quotation['service_description'] ?: 'No service work included in this quotation.') ?></strong></span></div>
                
            </div>
        </div>

        <div class="quotation-paper-table-wrap">
            <table class="quotation-paper-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Product</th>
                        <th style="width:70px">Qty</th>
                        <th style="width:100px">Unit Type</th>
                        <th style="width:130px">Unit Price</th>
                        <th style="width:130px">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td><?= (int) ($index + 1) ?></td>
                            <td>
                                <div class="quotation-paper-item-name"><?= e($item['product_name'] ?: 'Deleted Product') ?></div>
                            </td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><?= e($item['unit_type'] ?: 'PC') ?></td>
                            <td><?= e(format_currency($item['unit_price'])) ?></td>
                            <td><strong><?= e(format_currency($item['subtotal'])) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="quotation-paper-footer-grid">
            <div class="quotation-paper-panel">
                <div class="quotation-paper-label">Terms and Conditions</div>
                <div class="quotation-paper-note" style="white-space: pre-line;"><?= e($quotation['notes'] ?: $quotationDefaultNotes) ?></div>
            </div>
        </div>

        <div class="quotation-paper-totals quotation-paper-totals-full">
            <div class="quotation-paper-total-row">
                <span>Parts Subtotal</span>
                <strong><?= e(format_currency($quotation['subtotal_amount'])) ?></strong>
            </div>
            <div class="quotation-paper-total-row">
                <span>Service Repair Fee</span>
                <strong><?= e(format_currency($quotation['service_fee'])) ?></strong>
            </div>
            <div class="quotation-paper-total-row quotation-paper-grand-total">
                <span>Total Quotation Amount</span>
                <strong><?= e(format_currency($quotation['total_amount'])) ?></strong>
            </div>
        </div>

        <div class="quotation-paper-signoff">
            <div class="quotation-paper-signature">
                <span>Prepared By</span>
                <strong><?= e($quotation['prepared_by'] ?: 'System') ?></strong>
            </div>
            <div class="quotation-paper-signature">
                <span>Conforme / Accepted By</span>
                <strong>________________________</strong>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
(function () {
    var downloadBtn = document.getElementById('downloadQuotationPdfBtn');
    if (!downloadBtn || !window.jspdf) {
        return;
    }

    var pdfData = <?= $quotationPdfPayload ?>;
    var defaultLabel = downloadBtn.innerHTML;

    downloadBtn.addEventListener('click', async function () {
        downloadBtn.disabled = true;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF';

        try {
            var jsPDF = window.jspdf.jsPDF;
            var pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
                compress: true
            });

            var pageWidth = pdf.internal.pageSize.getWidth();
            var pageHeight = pdf.internal.pageSize.getHeight();
            var marginX = 15;
            var topMargin = 16;
            var contentWidth = pageWidth - (marginX * 2);
            var y = topMargin;

            function ensureSpace(requiredHeight) {
                if (y + requiredHeight <= pageHeight - 16) {
                    return;
                }
                pdf.addPage();
                y = topMargin;
            }

            function drawRoundedBlock(x, yPos, width, height) {
                pdf.setDrawColor(222, 222, 222);
                pdf.roundedRect(x, yPos, width, height, 2.5, 2.5);
            }

            function textLines(text, width, fontSize) {
                pdf.setFontSize(fontSize);
                return pdf.splitTextToSize(String(text || ''), width);
            }

            function textHeight(text, fontSize) {
                pdf.setFontSize(fontSize);
                return pdf.getTextDimensions(text).h;
            }

            function drawMetaRow(label, value, x, yPos, labelWidth) {
                pdf.setFont('helvetica', 'normal');
                pdf.setFontSize(7.4);
                pdf.setTextColor(92, 92, 92);
                pdf.text(label, x, yPos);
                pdf.setFont('helvetica', 'bold');
                pdf.setTextColor(20, 20, 20);
                pdf.text(String(value || ''), x + labelWidth, yPos);
            }

            function measureFieldStack(label, value, width, options) {
                var config = options || {};
                var labelSize = config.labelSize || 7.1;
                var valueSize = config.valueSize || 8.3;
                var lines = textLines(value, width, valueSize);

                return textHeight(label, labelSize) + 0.8 + textHeight(lines, valueSize) + (config.spacingAfter || 2.6);
            }

            function drawSectionHeading(title, yPos, xPos) {
                var x = typeof xPos === 'number' ? xPos : marginX;
                pdf.setFont('helvetica', 'bold');
                pdf.setFontSize(6.1);
                pdf.setTextColor(110, 110, 110);
                pdf.text(title, x, yPos);
                return yPos + textHeight(title, 6.1) + 1.6;
            }

            function drawFieldStack(label, value, x, yPos, width, options) {
                var config = options || {};
                var labelSize = config.labelSize || 7.1;
                var valueSize = config.valueSize || 8.3;
                var lines = textLines(value, width, valueSize);

                pdf.setFont('helvetica', 'normal');
                pdf.setFontSize(labelSize);
                pdf.setTextColor(95, 95, 95);
                pdf.text(label, x, yPos);
                yPos += textHeight(label, labelSize) + 0.8;

                pdf.setFont('helvetica', config.bold ? 'bold' : 'normal');
                pdf.setFontSize(valueSize);
                pdf.setTextColor(20, 20, 20);
                pdf.text(lines, x, yPos);

                return yPos + textHeight(lines, valueSize) + (config.spacingAfter || 2.6);
            }

            function drawValueBlock(value, x, yPos, width, options) {
                var config = options || {};
                var valueSize = config.valueSize || 8.3;
                var lines = textLines(value, width, valueSize);

                pdf.setFont('helvetica', config.bold ? 'bold' : 'normal');
                pdf.setFontSize(valueSize);
                pdf.setTextColor(20, 20, 20);
                pdf.text(lines, x, yPos);

                return yPos + textHeight(lines, valueSize) + (config.spacingAfter || 2.6);
            }

            function drawDivider(yPos) {
                pdf.setDrawColor(228, 228, 228);
                pdf.setLineWidth(0.2);
                pdf.line(marginX, yPos, pageWidth - marginX, yPos);
                return yPos + 4;
            }

            function loadImage(src) {
                return new Promise(function (resolve, reject) {
                    var image = new Image();
                    image.crossOrigin = 'anonymous';
                    image.onload = function () {
                        resolve(image);
                    };
                    image.onerror = reject;
                    image.src = src;
                });
            }

            var leftX = marginX;
            var rightX = pageWidth - marginX - 54;
            var headerImageLoaded = false;
            var headerBlockHeight = 19.5;

            if (pdfData.headerImageUrl) {
                try {
                    var headerImage = await loadImage(pdfData.headerImageUrl);
                    var headerImageWidth = 92;
                    var headerImageHeight = (headerImage.naturalHeight / headerImage.naturalWidth) * headerImageWidth;
                    pdf.addImage(headerImage, 'PNG', leftX, y - 4.5, headerImageWidth, headerImageHeight);
                    headerBlockHeight = Math.max(headerBlockHeight, headerImageHeight + 4.5);
                    headerImageLoaded = true;
                } catch (error) {
                    console.warn('Unable to load quotation header image for PDF export.', error);
                }
            }

            if (!headerImageLoaded) {
                pdf.setFont('helvetica', 'bold');
                pdf.setFontSize(11);
                pdf.text(pdfData.companyName, leftX, y);
                pdf.setFont('helvetica', 'normal');
                pdf.setFontSize(7.3);
                pdf.setTextColor(110, 110, 110);
                pdf.text(pdfData.companySubtitle, leftX, y + 5);
                pdf.text(pdfData.companyCaption, leftX, y + 10);
            }

            drawMetaRow('Quotation No.:', pdfData.quoteNo, rightX, y, 19);
            drawMetaRow('Date Issued:', pdfData.createdAt, rightX, y + 4.6, 19);
            drawMetaRow('Validity:', pdfData.validUntil, rightX, y + 9.2, 19);

            y += headerBlockHeight;
            pdf.setDrawColor(20, 20, 20);
            pdf.setLineWidth(0.6);
            pdf.line(marginX, y, pageWidth - marginX, y);
            y += 4.6;

            pdf.setFont('helvetica', 'normal');
            pdf.setFontSize(9.5);
            pdf.setTextColor(40, 40, 40);
            pdf.text('Subject', marginX, y);
            y += 3.8;

            pdf.setFont('helvetica', 'normal');
            pdf.setFontSize(7.3);
            pdf.setTextColor(95, 95, 95);
            (pdfData.subjectParagraphs || []).forEach(function (paragraph, index) {
                var subjectLines = textLines(paragraph, contentWidth, 7.3);
                pdf.text(subjectLines, marginX, y);
                y += textHeight(subjectLines, 7.3) + (index === (pdfData.subjectParagraphs.length - 1) ? 4.2 : 3.2);
            });

            var infoColumnGap = 10;
            var infoColumnWidth = (contentWidth - infoColumnGap) / 2;
            var leftInfoX = marginX;
            var rightInfoX = marginX + infoColumnWidth + infoColumnGap;

            var customerNameLines = textLines(pdfData.customerName, infoColumnWidth, 10);
            var customerSectionHeight =
                textHeight('CUSTOMER INFORMATION', 6.1) +
                1.6 +
                textHeight(customerNameLines, 10) + 2.4 +
                measureFieldStack('Contact No.', pdfData.customerContact, infoColumnWidth, {
                    bold: true,
                    valueSize: 8.2,
                    spacingAfter: 2
                }) +
                measureFieldStack('Address', pdfData.customerAddress, infoColumnWidth, {
                    bold: true,
                    valueSize: 8,
                    spacingAfter: 0
                });

            var quotationSectionHeight =
                textHeight('QUOTATION DETAILS', 6.1) +
                1.6 +
                measureFieldStack('Service Option', pdfData.serviceOption, infoColumnWidth, {
                    bold: true,
                    valueSize: 8.2,
                    spacingAfter: 2
                }) +
                measureFieldStack('Service Description', pdfData.serviceDescription, infoColumnWidth, {
                    bold: true,
                    valueSize: 8,
                    spacingAfter: 0
                });

            var infoSectionHeight = Math.max(customerSectionHeight, quotationSectionHeight);
            ensureSpace(infoSectionHeight + 4);
            var sectionStartY = y;

            var leftY = drawSectionHeading('CUSTOMER INFORMATION', sectionStartY, leftInfoX);
            leftY = drawValueBlock(pdfData.customerName, leftInfoX, leftY, infoColumnWidth, {
                bold: true,
                valueSize: 10,
                spacingAfter: 2.4
            });
            leftY = drawFieldStack('Contact No.', pdfData.customerContact, leftInfoX, leftY, infoColumnWidth, {
                bold: true,
                valueSize: 8.2,
                spacingAfter: 2
            });
            drawFieldStack('Address', pdfData.customerAddress, leftInfoX, leftY, infoColumnWidth, {
                bold: true,
                valueSize: 8,
                spacingAfter: 0
            });

            var rightY = drawSectionHeading('QUOTATION DETAILS', sectionStartY, rightInfoX);
            rightY = drawFieldStack('Service Option', pdfData.serviceOption, rightInfoX, rightY, infoColumnWidth, {
                bold: true,
                valueSize: 8.2,
                spacingAfter: 2
            });
            drawFieldStack('Service Description', pdfData.serviceDescription, rightInfoX, rightY, infoColumnWidth, {
                bold: true,
                valueSize: 8,
                spacingAfter: 0
            });

            y = drawDivider(sectionStartY + infoSectionHeight);

            function drawTableHeader() {
                drawRoundedBlock(marginX, y, contentWidth, 8);
                pdf.setFont('helvetica', 'bold');
                pdf.setFontSize(5.8);
                pdf.setTextColor(100, 100, 100);
                var col = {
                    n: marginX + 3,
                    product: marginX + 10,
                    qtyRight: marginX + 118,
                    unitType: marginX + 124,
                    unitRight: marginX + 164,
                    subtotalRight: pageWidth - marginX - 4
                };
                pdf.text('#', col.n, y + 5.2);
                pdf.text('PRODUCT', col.product, y + 5.2);
                pdf.text('QTY', col.qtyRight, y + 5.2, { align: 'right' });
                pdf.text('UNIT TYPE', col.unitType, y + 5.2);
                pdf.text('UNIT PRICE', col.unitRight, y + 5.2, { align: 'right' });
                pdf.text('SUBTOTAL', col.subtotalRight, y + 5.2, { align: 'right' });
                y += 8;
                return col;
            }

            var col = drawTableHeader();
            pdf.setLineWidth(0.2);
            pdf.setDrawColor(232, 232, 232);

            pdfData.items.forEach(function (item) {
                ensureSpace(8);
                pdf.line(marginX, y, pageWidth - marginX, y);
                y += 4.8;
                pdf.setFont('helvetica', 'normal');
                pdf.setFontSize(7.8);
                pdf.setTextColor(30, 30, 30);
                pdf.text(item.index, col.n, y);
                pdf.setFont('helvetica', 'bold');
                pdf.text(item.product, col.product, y);
                pdf.setFont('helvetica', 'normal');
                pdf.text(item.quantity, col.qtyRight, y, { align: 'right' });
                pdf.text(item.unitType, col.unitType, y);
                pdf.text(item.unitPrice, col.unitRight, y, { align: 'right' });
                pdf.setFont('helvetica', 'bold');
                pdf.text(item.subtotal, col.subtotalRight, y, { align: 'right' });
                y += 2.4;
            });

            pdf.line(marginX, y, pageWidth - marginX, y);
            y += 3;

            var totalsHeight = 14.5;
            ensureSpace(totalsHeight + 12);
            pdf.setFont('helvetica', 'normal');
            pdf.setFontSize(8.2);
            pdf.setTextColor(40, 40, 40);
            pdf.text('Parts Subtotal', marginX + 2, y + 4.4);
            pdf.text('Service Repair Fee', marginX + 2, y + 9.2);
            pdf.setFont('helvetica', 'bold');
            pdf.text(pdfData.subtotalAmount, pageWidth - marginX - 2, y + 4.4, { align: 'right' });
            pdf.text(pdfData.serviceFee, pageWidth - marginX - 2, y + 9.2, { align: 'right' });
            pdf.setDrawColor(20, 20, 20);
            pdf.setLineWidth(0.6);
            pdf.line(marginX + 2, y + 11.3, pageWidth - marginX - 2, y + 11.3);
            pdf.setFontSize(9.2);
            pdf.text('Total Quotation Amount', marginX + 2, y + 14.6);
            pdf.text(pdfData.totalAmount, pageWidth - marginX - 2, y + 14.6, { align: 'right' });
            y += totalsHeight + 8;

            var notesLines = textLines(pdfData.notes, contentWidth - 8, 7.4);
            var notesHeight = 10.5 + textHeight(notesLines, 7.4);
            ensureSpace(notesHeight + 26);
            drawRoundedBlock(marginX, y, contentWidth, notesHeight);
            pdf.setFont('helvetica', 'bold');
            pdf.setFontSize(6.7);
            pdf.setTextColor(142, 66, 66);
            pdf.text(pdfData.termsLabel || 'Terms and Conditions', marginX + 4, y + 5.7);
            pdf.setFont('helvetica', 'normal');
            pdf.setFontSize(7.4);
            pdf.setTextColor(126, 78, 78);
            pdf.text(notesLines, marginX + 4, y + 9.3);
            y += notesHeight + 42;

            pdf.setDrawColor(212, 212, 212);
            pdf.setLineWidth(0.2);
            pdf.line(marginX, y, pageWidth - marginX, y);
            y += 6;

            var signatureWidth = 56;
            pdf.setFont('helvetica', 'normal');
            pdf.setFontSize(7.4);
            pdf.setTextColor(115, 115, 115);
            pdf.text('Prepared By', marginX, y);
            pdf.text('Conforme / Accepted By', pageWidth - marginX - signatureWidth, y);
            y += 6.5;

            pdf.setFont('helvetica', 'bold');
            pdf.setFontSize(8.8);
            pdf.setTextColor(30, 30, 30);
            pdf.text(pdfData.preparedBy, marginX, y);
            pdf.setDrawColor(188, 188, 188);
            pdf.setLineWidth(0.2);
            pdf.line(pageWidth - marginX - signatureWidth, y + 1.4, pageWidth - marginX, y + 1.4);

            pdf.save('quotation-<?= e($quotation['quote_no']) ?>.pdf');
        } catch (error) {
            console.error('Quotation PDF generation failed:', error);
            alert('Unable to generate the quotation PDF right now.');
        } finally {
            downloadBtn.disabled = false;
            downloadBtn.innerHTML = defaultLabel;
        }
    });
})();
</script>
