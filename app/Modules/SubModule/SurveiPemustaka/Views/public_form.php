<section class="py-5 section-tone-1" id="survei" aria-labelledby="survey-heading">
    <div class="container my-4">
        <h3 class="fw-bold mb-3 d-flex align-items-center section-title" id="survey-heading">
            <span class="bg-brand rounded-pill me-3" style="width: 5px; height: 30px;"></span>
            Survei Pemustaka
        </h3>
        <p class="text-secondary mb-4">Pendapat Anda membantu meningkatkan layanan perpustakaan.</p>

        <?php if (session()->getFlashdata('survey_success')): ?>
            <div class="alert alert-success" role="status"><?= esc(session()->getFlashdata('survey_success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('survey_error')): ?>
            <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('survey_error')) ?></div>
        <?php endif; ?>

        <?php if (empty($surveys)): ?>
            <div class="bg-white border rounded-4 p-4 text-secondary">Belum ada survei aktif saat ini.</div>
        <?php endif; ?>
        <?php $oldAnswers = old('jawaban'); $oldAnswers = is_array($oldAnswers) ? $oldAnswers : []; ?>
        <?php foreach ($surveys ?? [] as $survey): ?>
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4 p-lg-5">
                    <h4 class="fw-bold text-dark mb-2"><?= esc($survey['NamaSurvey']) ?></h4>
                    <?php if (!empty($survey['RedaksiAwal'])): ?>
                        <p class="text-secondary mb-4"><?= nl2br(esc($survey['RedaksiAwal'])) ?></p>
                    <?php endif; ?>
                    <?php if (session()->get('survey_done_' . $survey['ID'])): ?>
                        <p class="alert alert-success mb-0">Survei ini sudah Anda isi. <?= esc($survey['RedaksiAkhir'] ?: 'Terima kasih atas partisipasi Anda.') ?></p>
                    <?php elseif (empty($survey['questions'])): ?>
                        <p class="text-secondary mb-0">Pertanyaan survei belum tersedia.</p>
                    <?php else: ?>
                        <form action="<?= site_url('home/survei/' . $survey['ID']) ?>" method="post">
                            <?= csrf_field() ?>
                            <?php foreach ($survey['questions'] as $index => $question): ?>
                                <?php
                                $questionId = (int) $question['ID'];
                                $multiple = (string) $question['IsCanMultipleAnswer'] === '1' || $question['IsCanMultipleAnswer'] === "\x01";
                                $required = (string) $question['IsMandatory'] === '1' || $question['IsMandatory'] === "\x01";
                                $isChoice = strcasecmp((string) $question['JenisPertanyaan'], 'Pilihan') === 0;
                                $previous = $oldAnswers[$questionId] ?? null;
                                ?>
                                <fieldset class="border-top pt-4 mt-4">
                                    <legend class="fs-6 fw-semibold text-dark mb-3">
                                        <?= $index + 1 ?>. <?= nl2br(esc($question['Pertanyaan'])) ?>
                                        <?php if ($required): ?><span class="text-danger" aria-label="wajib diisi">*</span><?php endif; ?>
                                    </legend>
                                    <?php if ($isChoice && empty($question['options'])): ?>
                                        <p class="text-secondary">Pilihan jawaban belum tersedia.</p>
                                    <?php elseif ($isChoice): ?>
                                        <div class="<?= $question['Orientation'] === 'Horizontal' ? 'd-flex flex-wrap gap-3' : 'd-grid gap-2' ?>">
                                            <?php foreach ($question['options'] as $option): ?>
                                                <?php $optionId = (int) $option['ID']; ?>
                                                <label class="d-flex align-items-start gap-2 text-dark">
                                                    <input class="form-check-input mt-1" type="<?= $multiple ? 'checkbox' : 'radio' ?>"
                                                        name="jawaban[<?= $questionId ?>]<?= $multiple ? '[]' : '' ?>"
                                                        value="<?= $optionId ?>"
                                                        <?= in_array((string) $optionId, array_map('strval', (array) $previous), true) ? 'checked' : '' ?>
                                                        <?= $required && !$multiple ? 'required' : '' ?>>
                                                    <span><?= esc($option['Pilihan']) ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <textarea class="form-control" name="jawaban[<?= $questionId ?>]" rows="3" maxlength="2000"
                                            <?= $required ? 'required' : '' ?> aria-label="<?= esc($question['Pertanyaan'], 'attr') ?>"><?= esc(is_string($previous) ? $previous : '') ?></textarea>
                                    <?php endif; ?>
                                </fieldset>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-brand rounded-pill px-4 mt-4">Kirim Jawaban</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>