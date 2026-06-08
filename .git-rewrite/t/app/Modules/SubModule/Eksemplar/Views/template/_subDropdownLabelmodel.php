   <select class="form-control" id="model_kertas" name="model_kertas">
       <?php foreach ($model as $key => $src) : ?>
           <option value="<?= $key ?>">
               <?= $src ?>
           </option>
       <?php endforeach ?>
   </select>