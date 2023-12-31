@extends('layouts.users')

@section('title', 'Painel do Aluno')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col s12 m6">
                <div class="card">
                   <form action="{{route('profile.updateImage')}}" id="form_profile" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                        <div class="card-image">
                            <!-- Imagem de perfil -->
                            <div class="image-container">
                                <div class="image-preview">
                                    <img src="/img/profile_photo_path/{{Auth::user()->profile_photo_path}}" class="circle responsive-img materialboxed" id="profile-img-mobile" alt="Imagem de Perfil">
                                </div>
                                
                                <input name="profile_photo_path" type="file" id="file-input" style="display: none">
                                <a class="btn-floating halfway-fab waves-effect waves-light red" id="upload-button">
                                    <i class="material-icons">add_a_photo</i>
                                </a>
                            </div>
                        </div>
                    
                        <div class="card-content">
                            <span class="card-title center" id="profile-title-mobile">{{ $userProfile->name }}</span>
                            <span id="profile-subtitle-mobile">Email:</span>
                            <p id="profile-text-mobile">{{ $userProfile->email }}</p>
                            <span id="profile-subtitle-mobile">Data de Nascimento:</span>
                            <p id="profile-text-mobile">{{ \Carbon\Carbon::parse($userProfile->date)->format('d/m/Y') }}</p>
                            <span id="profile-subtitle-mobile">Sexo:</span>
                            <p id="profile-text-mobile">{{ $userProfile->sexo }}</p>
                            <span id="profile-subtitle-mobile">Telefone:</span>
                            <p id="profile-text-mobile">{{ $userProfile->phone }}</p>
                            <div class="center">
                                <button type="submit" class="btn waves-effect waves-light deep-orange accent-3"><i class="material-icons left">rotate_right</i>Atualizar Foto</button>
                            </div>
                        </div>
                   </form>
                    <div class="card-action">
                        <div class="row">
                            
                            <span class="card-title center" id="profile-title-mobile">Calcular IMC</span>
                            
                            <div class="input-field col s12" id="inputField">
                                <i class="material-icons prefix">monitor_weight</i>
                                <input name="profile_kg" id="icon_prefix_weight" type="text" class="validate" oninput="addKg(this)">
                                <label for="icon_prefix_weight">Peso</label>
                            </div>
                            
                            <div class="input-field col s12" id="inputField">
                                <i class="material-icons prefix">height</i>
                                <input name="profile_alt" id="icon_prefix_height" type="text" class="validate" oninput="addCm(this)">
                                <label for="icon_prefix_height">Altura</label>
                            </div>

                            <div class="center">
                                <a class="center waves-effect waves-light btn" id="calculateButton"><i class="material-icons left">calculate</i>Calcular</a>
                            </div>

                            <p id="result_profile_imc"></p>
                        </div>
                    </div>

                    <div class="card-action">
                        <div class="row">
                            <span class="card-title center" id="profile-title-mobile">Fichas</span>
                            @if(count($fichas) > 0)
                                @foreach ($fichas as $ficha)
                                    <p id="profile-text-mobile">{{ $ficha->name_training }}</p>
                                @endforeach
                                <span id="profile-subtitle-mobile">Total de fichas concluídas: {{ count($statistics) }}</span>
                            @else
                                <br><span id="profile-subtitle-mobile">Você ainda não possui ficha:</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-action">
                        <div class="row">
                            <span class="card-title center" id="profile-title-mobile">Avaliações</span>
                            @if(count($fichas) > 0)
                                @foreach ($assessments as $assessment)
                                    <p id="profile-text-mobile">{{ $assessment->goal }}</p>
                                    <p id="profile-text-mobile">Prazo: {{ $assessment->term }}</p>
                                    <p id="profile-text-mobile">Altura: {{ $assessment->height }}</p>
                                    <p id="profile-text-mobile">Peso: {{ $assessment->weight }}</p>
                                    <span id="profile-subtitle-mobile">-=-=-=-=-=-=-=-=-=-=-=</span>
                                @endforeach
                                <br><span id="profile-subtitle-mobile">Total de avaliaçoes: {{ count($assessments) }}</span>
                            @else
                                <br><span id="profile-subtitle-mobile">Você ainda não possui avaliação:</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de alerta -->
    <div id="modal-alerta" class="modal">
        <div class="modal-content">
            <i class="material-icons" id="modal-icon-alert">info</i>
            <h4>Confirmação</h4>
            <p>Deseja realmente mudar a foto de perfil ?</p>
        </div>

        <div class="modal-footer">
            <a href="#" class="modal-close waves-effect waves-green btn-flat right" id="cancelBtn">Cancelar</a>
            <a href="#" class="modal-close waves-effect waves-green btn light-blue darken-4 left" id="sendBtn">Sim</a>
        </div>
    </div>
    
@endsection

@section('script')
    <script>

    document.addEventListener('DOMContentLoaded', function() {
        let modal = document.getElementById('modal-alerta');
        let instance = M.Modal.init(modal);

        let form = document.querySelector('#form_profile');

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            instance.open();
        });

        let cancelBtn = document.querySelector('.modal-footer .modal-close');

        cancelBtn.addEventListener('click', function() {
            instance.close();
        });

        let sendBtn = document.getElementById('sendBtn');

        sendBtn.addEventListener('click', function() {
            form.submit();
        });
    });

        // Referencie os elementos HTML que você adicionou
        const fileInput = document.getElementById('file-input');
        const previewImage = document.getElementById('profile-img-mobile'); // Alterei para usar o mesmo ID da imagem original
        const uploadButton = document.getElementById('upload-button');

        // Adicione um evento de clique ao botão de upload
        uploadButton.addEventListener('click', function () {
            fileInput.click();
        });

        // Adicione um evento de alteração ao input de arquivo
        fileInput.addEventListener('change', function () {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        });
        
        function addKg(input) {
            const inputValue = input.value;
            const cleanedValue = inputValue.replace(/[^0-9.]/g, ''); // Remove caracteres não numéricos, mantendo apenas números e pontos
            input.value = cleanedValue + ' Kg'; // Adicione 'Kg' ao valor limpo

            // Salvar o valor no localStorage
            localStorage.setItem('weight', cleanedValue);
        }

        function addCm(input) {
            const inputValue = input.value;
            const cleanedValue = inputValue.replace(/[^0-9]/g, ''); // Remove caracteres não numéricos, mantendo apenas números
            input.value = cleanedValue + ' cm'; // Adicione 'cm' ao valor limpo

             // Salvar o valor no localStorage
            localStorage.setItem('height', cleanedValue);
        }

        document.getElementById('calculateButton').addEventListener('click', calculateBMI);

    function calculateBMI() {
        const weightInput = document.getElementById('icon_prefix_weight');
        const heightInput = document.getElementById('icon_prefix_height');
        const resultElement = document.getElementById('result_profile_imc');

        // Recuperar os valores do localStorage
        const savedWeight = localStorage.getItem('weight');
        const savedHeight = localStorage.getItem('height');

        // Usar os valores salvos se existirem
        if (savedWeight) {
            weightInput.value = savedWeight + ' Kg';
        }
        if (savedHeight) {
            heightInput.value = savedHeight + ' cm';
        }

        const weight = parseFloat(weightInput.value);
        const height = parseFloat(heightInput.value);

        if (!isNaN(weight) && !isNaN(height)) {
            const bmi = weight / ((height / 100) ** 2);
                resultElement.innerHTML = `IMC: ${bmi.toFixed(2)} <br><br><a href="https://www.tuasaude.com/imc/" target="_blank">Verifique a tabela do IMC clicando aqui!!</a>`;
        } else {
            resultElement.innerText = 'Preencha peso e altura válidos.';
        }
    }

    </script>
@endsection