@extends('layouts.admin')

@section('title', 'Estatisticas')

@section('content')

  <!--Divs para titulo e Reporte -->
  <div class="row">
    <div class="col s12 l10">
      <h3 id="homeTitle">Painel de Estatísticas </h3>
      <a class="waves-effect waves-light btn modal-trigger blue accent-2" href="{{ route('admin.home') }}"><i class="material-icons left" >arrow_back</i>Voltar</a>
    </div>
  </div>

  <!-- Inicio de conteudo -->
  <div class="row">
    <div class="col s12">
      <div class="card ">
        <canvas id="chart"></canvas>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12">
      <div class="card ">
        <canvas id="graficoUsuariosPorMes" width="400" height="200"></canvas>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12">
      <div class="card ">
        <canvas id="graficoFichasPorMes" width="400" height="200"></canvas>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12">
      <div class="card ">
        <canvas id="graficoAssessmentPorMes" width="400" height="200"></canvas>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col s12">
      <div class="card ">
        <canvas id="graficoCalledPorMes" width="400" height="400"></canvas>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-content">
      <div class="col s12 l12">
        <h3 class="center" id="titleColor" >Exercícios Finalizados</h3>
      </div>
      
      <input type="text" id="search" placeholder="Pesquisar...">
      <div id="total-records" class="total-records"></div>
      <table class="highlight striped centered" id="form_table_group_muscle">
        <thead>
          <tr>
            <th>Aluno</th>
            <th>Ficha</th>
            <th>Dia</th>
          </tr>
        </thead>
        
        <tbody id="table-body">
          @foreach($statistics as $statistic)
            <tr>
              <td id="statistic-table">{{ $statistic->name }}</td>
              <td id="statistic-table">{{ $statistic->name_training }}</td>
              <td id="statistic-table">{{ \Carbon\Carbon::parse($statistic->created_at)->format('d/m/Y H:i:s') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div id="no-results" class="no-results-message" style="display: none;">Nenhum registro encontrado</div>
      <div id="total-records" class="total-records"></div>
    </div>
  </div>


  <div class="card">
    <div class="card-content">
      <div class="col s12 l12">
        <h3 class="center" id="titleColor" >Ranking de Exercícios Finalizados</h3>
      </div>

      <table class="highlight striped centered" id="form_table_group_muscle">
        <thead>
          <tr>
            <th>Posição</th>
            <th>Aluno</th>
            <th>Total</th>
          </tr>
        </thead>
        
        <tbody id="table-body">
          @php $position = 1; @endphp
          @foreach($topStudentsTotals as $studentName => $totalFichas)
            <tr>
              <td id="statistic-table">{{ $position}}º</td>
              <td id="statistic-table">{{ $studentName }}</td>
              <td id="statistic-table">{{ $totalFichas }}</td>
            </tr>
            @php $position++; @endphp
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

@section('script')
<script>
  // Função para filtrar os registros da tabela
  function filterTable() {
      let input = document.getElementById('search');
      let filter = input.value.toLowerCase();
      let rows = document.getElementById('table-body').getElementsByTagName('tr');
      let noResultsMessage = document.getElementById('no-results');
      let totalRecords = document.getElementById('total-records');
      let resultsFound = false;
      let count = 0;

      for (let i = 0; i < rows.length; i++) {
        let nome = rows[i].getElementsByTagName('td')[0].innerText.toLowerCase();
        let acao = rows[i].getElementsByTagName('td')[1].innerText.toLowerCase();

        if (nome.indexOf(filter) > -1 || acao.indexOf(filter) > -1) {
          rows[i].style.display = '';
          resultsFound = true;
          count++;
        } else {
          rows[i].style.display = 'none';
        }
      }

      if (resultsFound) {
        noResultsMessage.style.display = 'none';
      } else {
        noResultsMessage.style.display = 'block';
      }

      totalRecords.innerText = "Total de registros encontrados: " + count;
    }

    // Evento de input para acionar a filtragem ao digitar na caixa de pesquisa
    document.getElementById('search').addEventListener('input', filterTable);


    document.addEventListener("DOMContentLoaded", function() {
      fetch('/estatisticas/users-por-mes')
        .then(response => response.json())
        .then(data => {
        const meses = [];
        const usuariosCriados = [];

        data.forEach(item => {
            meses.push(`Mês ${item.month}`);
            usuariosCriados.push(item.total_users);
        });

        var ctx = document.getElementById('graficoUsuariosPorMes').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Usuários Criados por Mês',
                    data: usuariosCriados,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });

    fetch('/estatisticas/fichas-por-mes')
            .then(response => response.json())
            .then(data => {
                const meses = [];
                const fichasCriadas = [];

                data.forEach(item => {
                    meses.push(`Mês ${item.month}`);
                    fichasCriadas.push(item.total_fichas);
                });

                var ctx = document.getElementById('graficoFichasPorMes').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: meses,
                        datasets: [{
                            label: 'Fichas Criadas por Mês',
                            data: fichasCriadas,
                            backgroundColor: 'rgba(255, 206, 86, 0.5)', 
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

            fetch('/estatisticas/assessment-por-mes')
            .then(response => response.json())
            .then(data => {
                const meses = [];
                const avaliacoesCriadas = [];

                data.forEach(item => {
                    meses.push(`Mês ${item.month}`);
                    avaliacoesCriadas.push(item.total_assessment);
                });

                var ctx = document.getElementById('graficoAssessmentPorMes').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: meses,
                        datasets: [{
                            label: 'Avaliações Criadas por Mês',
                            data: avaliacoesCriadas,
                            backgroundColor: 'rgba(139, 69, 19, 0.5)', // Cor marrom
                            borderColor: 'rgba(139, 69, 19, 1)', // Cor marrom
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
            
            fetch('/estatisticas/called-por-mes')
            .then(response => response.json())
            .then(data => {
                const meses = [];
                const chamados = [];

                data.forEach(item => {
                    meses.push(`Mês ${item.month}`);
                    chamados.push(item.total_called);
                });

                var ctx = document.getElementById('graficoCalledPorMes').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: meses,
                        datasets: [{
                            label: 'Chamados Criados por Mês',
                            data: chamados,
                            backgroundColor: 'rgba(0, 0, 0, 0.5)', // Cor marrom
                            borderColor: 'rgba(139, 69, 19, 1)', // Cor marrom
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
          
  });
</script>
@endsection