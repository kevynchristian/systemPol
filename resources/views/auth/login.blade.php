<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>System - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css"/>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="shortcut icon" type="image/png" href="https://recargatatica.files.wordpress.com/2010/08/caveira.jpg?w=640"/>
</head>
<!--Coded with love by Mutiullah Samim-->

<body>
    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <div class="d-flex justify-content-center">
                    <div class="brand_logo_container">
                        <img src="https://recargatatica.files.wordpress.com/2010/08/caveira.jpg?w=640" class="brand_logo" alt="Logo">
                    </div>
                </div>
                <div class="d-flex justify-content-center form_container">
                    <form action="{{ route('login.authenticate') }}" method="POST">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="nickname" class="form-control input_user" value="" placeholder="Seu Nick do Habbo">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" name="senha" class="form-control input_pass" value="" placeholder="Sua Senha do System">
                        </div>

                        <div class="d-flex justify-content-center mt-3 login_container">
                            <button type="submit" name="button" class="btn login_btn">Login</button>
                        </div>
                    </form>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-center links">

                        Não tem uma conta? <a href="#" data-bs-toggle="modal" data-bs-target="#cadastro">Criar</a>
                    </div>
                    <div class="d-flex justify-content-center links">
                        <a href="#">Esqueceu sua senha?</a>
                    </div>
                </div>
                <hr>
                <p class="text-center fst-italic text-muted">System {{ date('Y') }} &copy; - Todos os direitos reservados.</p>
            </div>
        </div>
    </div>

<!-- Modal cadastro -->
<div class="modal fade" id="cadastro">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Registro de conta</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <!-- Modal body -->
        <div class="modal-body d-flex justify-content-center">
            <div class="user_card_create">
                <form id="formCreate">
                    <p class="modal-title mb-3 mt-2">Crie sua conta para acessar o system</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="nickname" class="form-control input_user" value="" placeholder="Seu Nick do Habbo" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control input_user" value="" placeholder="Seu Email" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" class="form-control input_pass" value="" placeholder="Sua Senha do System" required>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-shield-alt"></i></span>
                        <input type="text" id="tokenInput" readonly  class="form-control input_pass" value="{{ session('tokenMissao') }}">
                        <span class="btn btn-outline-primary copy-icon" onclick="copyTokenToClipboard(this)"><i class="fas fa-copy"></i></span>
                    </div>
                    <p class="text-danger mb-5">*Cole o código na sua missão do Habbo</p>

                    <div class="d-flex justify-content-center mt-3 login_container">
                        <button type="button" onclick="createAccount()" class="btn login_btn">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>

      </div>
    </div>
  </div>
</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
<script>
    function createAccount() {
        var formData = $('#formCreate').serialize();
        formData += '&_token={{ csrf_token() }}';
        $.ajax({
            type: "POST",
            url: "{{ route('login.register') }}",
            data: formData,
            success: function (response) {
                if(response.error == false){
                    iziToast.success({
                        title: 'Conta criada!',
                        message: 'Sua conta foi criada com sucesso.',
                    });
                    setInterval(fecharModal, 1500);
                }else{
                    iziToast.error({
                        title: 'Erro!',
                        message: 'Copie novamente o código e tente novamente.',
                    });
                }
            }
        });
    }

    function fecharModal(){
        // Selecione o botão pelo seletor de classe ou ID
        var btnClose = document.querySelector('.btn-close');

        // Simule o clique no botão
        btnClose.click();
    }

</script>
<script>
    function copyTokenToClipboard(iconElement) {
        var tokenInput = document.getElementById('tokenInput');
        tokenInput.select();
        document.execCommand('copy');
        iconElement.innerHTML = '<i class="fas fa-check"></i>'; // Altera o ícone para fa-check
        iconElement.classList.remove('btn-outline-primary'); // Remove a classe btn-outline-primary
        iconElement.classList.add('btn-success'); // Adiciona a classe btn-success
        // Você pode adicionar aqui uma mensagem ou qualquer outra ação depois de copiar
        // Por exemplo, desabilitar o ícone de cópia após a cópia ser bem-sucedida
        iconElement.onclick = null; // Remove o evento de clique do ícone
        iziToast.success({
            title: 'Código Copiado!',
            message: 'O código foi copiado, agora você deve colar na sua missão.',
        });
    }
</script>

{!! Toastr::message() !!}

</html>
