<?php

$pageTitle  = 'Home';
$activePage = 'search';

include __DIR__ . '/templates/header.php';
?>

  <main class="app-main">
    <div class="container">

      <div class="page-header">
        <div>
          <h1 class="page-title">Search</h1>
          <p class="page-subtitle">Data filtering.</p>
        </div>
      </div>

      <div class="grid-2col">

          <div class="card" id="search-form">
            <h2 class="card-title">FILTERS</h2>

            <div class="form-group">
              <label class="form-label" for="s-table">Table</label>
              <select class="form-control" id="s-table" name="table">
                <option value="">Select a table...</option>
                <option value="seizures">Seizures</option>
                <option value="emergencies">Emergencies</option>
                <option value="campaigns_projects">Campaigns Projects</option>
                <option value="prevention_activities">Prevention Activities</option>
                <option value="crimes_general">Crimes-General</option>
                <option value="crimes_sex">Crimes-Sex</option>
                <option value="crimes_law">Crimes-Law</option>
                <option value="crimes_sentences">Crimes-Sentences</option>
                <option value="criminal_groups">Criminal Groups</option>
              </select>
            </div>

            <div id="dynamic-filters"></div>

            <div style="display: flex; flex-direction: column; gap: var(--space-3);">
              <button class="btn btn-primary" type="submit" id="btn-search">Search</button>
              <button class="btn btn-ghost btn-sm" type="reset" id="btn-clear">Reset</button>
            </div>
          </div>
        
        <div>
          <div class="card" id="results-empty-state">
            <div>
              Results
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

</div>

</body>
</html>

<script src="js/search-filters.js"></script>
