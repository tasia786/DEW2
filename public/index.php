<?php

$pageTitle  = 'Home';
$activePage = 'search';

include __DIR__ . '/templates/header.php';
?>

  <main class="app-main">
    <div class="container">

      <div class="page-header">
        <div>
          <h1 class="page-title">Căutare</h1>
          <p class="page-subtitle">Filtrează datele.</p>
        </div>
      </div>

      <div class="grid-2col">

          <form class="card" id="search-form">
            <h2 class="card-title">FILTRE</h2>

            <div class="form-group">
              <label class="form-label" for="s-table">Tabel</label>
              <select class="form-control" id="s-table" name="table">
                <option value="">Selectează un tabel..</option>
                <option value="seizures">Capturi</option>
                <option value="emergencies">Urgențe</option>
                <option value="campaigns_projects">Campanii și Proiecte</option>
                <option value="prevention_activities">Activități Prevenire</option>
                <option value="crimes_general">Infracțiuni General</option>
                <option value="crimes_sex">Infracțiuni Sex</option>
                <option value="crimes_law">Infracțiuni Lege</option>
                <option value="crimes_sentences">Pedepse</option>
                <option value="criminal_groups">Grupări Infracționale</option>
              </select>
            </div>

            <div id="dynamic-filters"></div>

            <div style="display: flex; flex-direction: column; gap: var(--space-3);">
              <button class="btn btn-primary" type="submit" id="btn-search">Caută</button>
              <button class="btn btn-ghost btn-sm" type="reset" id="btn-clear">Reset</button>
            </div>
          </form>
        
        <div>
          <div class="card" id="results-empty-state">
            <div>
              Loading..
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

</div>

</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/config.js"></script>
<script src="js/utils.js"></script>
<script src="js/filter.js"></script>
<script src="js/result.js"></script>
<script src="js/search.js"></script>
