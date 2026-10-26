<?php
// Gemeinsames Formular für "Buch hinzufügen" und "Buch bearbeiten"
//$w (Werte), $fehler (Liste), $genres, $knopf (Beschriftung)
?>

<?php foreach ($fehler as $f): ?><p class="fehler"><?= e($f) ?></p><?php endforeach; ?>
<form method="post" class="form zweispaltig">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>Titel * <input name="titel" value="<?= e($w['titel'] ?? '') ?>" required maxlength="150"></label>
  <label>Autor * <input name="autor" value="<?= e($w['autor'] ?? '') ?>" required maxlength="100"></label>
  <label>Erscheinungsjahr <input name="erscheinungsjahr" value="<?= e($w['erscheinungsjahr'] ?? '') ?>" inputmode="numeric" maxlength="4"></label>
  <label>Genre
    <select name="genre_id">
      <option value="">– kein Genre –</option>
      <?php foreach ($genres as $g): ?>
        <option value="<?= (int)$g['id'] ?>" <?= (string)($w['genre_id'] ?? '') === (string)$g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Status
    <select name="status">
      <?php foreach (STATUS as $key => $label): ?>
        <option value="<?= e($key) ?>" <?= ($w['status'] ?? 'moechte_lesen') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Bewertung
    <select name="bewertung">
      <option value="">– nicht bewertet –</option>
      <?php for ($i = 0; $i <= 5; $i++): ?>
        <option value="<?= $i ?>" <?= (string)($w['bewertung'] ?? '') === (string)$i ? 'selected' : '' ?>><?= $i ?> <?= $i === 1 ? 'Stern' : 'Sterne' ?></option>
      <?php endfor; ?>
    </select>
  </label>
  <label>Gemocht?
    <select name="gemocht">
      <option value="">– keine Angabe –</option>
      <option value="1" <?= (string)($w['gemocht'] ?? '') === '1' ? 'selected' : '' ?>>Gemocht</option>
      <option value="0" <?= (string)($w['gemocht'] ?? '') === '0' ? 'selected' : '' ?>>Nicht gemocht</option>
    </select>
  </label>
  <label>Cover (Bild-Link) <input name="cover" value="<?= e($w['cover'] ?? '') ?>" placeholder="https://…" maxlength="255"></label>
  <label>Angefangen am <input type="date" name="startdatum" value="<?= e($w['startdatum'] ?? '') ?>"></label>
  <label>Beendet am <input type="date" name="enddatum" value="<?= e($w['enddatum'] ?? '') ?>"></label>
  <label class="breit">Beschreibung <textarea name="beschreibung" rows="4"><?= e($w['beschreibung'] ?? '') ?></textarea></label>
  <label class="breit">Meine Rezension <textarea name="rezension" rows="5"><?= e($w['rezension'] ?? '') ?></textarea></label>
  <div class="breit aktionen">
    <button class="knopf" type="submit"><?= e($knopf) ?></button>
    <a class="knopf hell" href="bibliothek.php">Abbrechen</a>
  </div>
</form>
