<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S.I.G.O. // Acesso Tático</title>

    <link rel="shortcut icon" href="https://i.imgur.com/k9Q6E1p.png" type="image/png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto+Mono:wght@300;400&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/login_tactical.css') }}">
</head>

<body>
    <div id="particles-js"></div>
    <div class="scanline"></div>

    <main class="login-container">
        <div class="login-card">
            <div class="terminal-header">
                <div class="header-dots">
                    <span></span><span></span><span></span>
                </div>
                <div class="header-title">S.I.G.O. - TERMINAL DE ACESSO</div>
            </div>

            <div class="card-content">
                <div class="d-flex justify-content-center mb-4">
                    <img src="{{ asset('img/logo.jpg') }}" class="brand_logo" alt="Logo">
                </div>

                <h3 class="text-center glitch" data-text="AUTENTICAÇÃO NECESSÁRIA">AUTENTICAÇÃO NECESSÁRIA</h3>

                <form action="{{ route('login.authenticate') }}" method="POST" novalidate>
                    @csrf
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fa fa-user-shield fa-fw"></i></span>
                        <input type="text" name="nickname" id="nickname" class="form-control"
                            placeholder="ID DE OPERADOR (NICKNAME)" required>
                    </div>

                    <div class="input-group mb-4">
                        <span class="input-group-text"><i class="fa fa-key fa-fw"></i></span>
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="SENHA DE ACESSO" required>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn login_btn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="btn-text">>> INICIAR SESSÃO</span>
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center links">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">SOLICITAR ACESSO</a>
                    <span class="mx-2">|</span>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#recoveryModal">RECUPERAR CREDENCIAIS</a>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-color); border-color: var(--border-color);">
                <div class="modal-header" style="border-bottom-color: var(--border-color);">
                    <h5 class="modal-title" id="registerModalLabel"
                        style="font-family: var(--font-primary); color: var(--primary-color);">REGISTRO DE OPERADOR</h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted text-center mb-3">Preencha seus dados para solicitar acesso.</p>
                    <form id="formCreate">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user fa-fw"></i></span>
                            <input type="text" name="nickname" class="form-control" placeholder="NICKNAME HABBO"
                                required>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-envelope fa-fw"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="SEU EMAIL"
                                required>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-key fa-fw"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="CRIE UMA SENHA"
                                required>
                        </div>
                        <label class="form-label small text-muted">Código de Verificação</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fa fa-shield-alt fa-fw"></i></span>
                            <input type="text" id="tokenInput" readonly class="form-control"
                                value="{{ session('missionToken') }}">
                            <button type="button" class="btn btn-outline-secondary copy-icon"
                                onclick="copyToken(this)"
                                style="border-color: var(--border-color); color: var(--primary-color);">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="small text-center" style="color: var(--primary-color);">*Copie o código e cole na
                            sua **missão** dentro do Habbo.</p>
                    </form>
                </div>
                <div class="modal-footer" style="border-top-color: var(--border-color);">
                    <button type="button" class="btn login_btn" onclick="createAccount(this)">
                        <span class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        <span class="btn-text">FINALIZAR REGISTRO</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--bg-color); border-color: var(--border-color);">
                <div class="modal-header" style="border-bottom-color: var(--border-color);">
                    <h5 class="modal-title" id="recoveryModalLabel"
                        style="font-family: var(--font-primary); color: var(--primary-color);">RECUPERAÇÃO DE
                        CREDENCIAIS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted text-center mb-3">Informe os dados para redefinir sua senha.</p>
                    <form id="formRecovery">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user-shield fa-fw"></i></span>
                            <input type="text" name="nickname" class="form-control"
                                placeholder="SEU NICKNAME HABBO" required>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-key fa-fw"></i></span>
                            <input type="password" name="password" class="form-control"
                                placeholder="DIGITE A NOVA SENHA" required>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-key fa-fw"></i></span>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="CONFIRME A NOVA SENHA" required>
                        </div>

                        <label class="form-label small text-muted">Código de Verificação</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fa fa-shield-alt fa-fw"></i></span>
                            <input type="text" id="tokenInputRecovery" readonly class="form-control"
                                value="{{ session('missionToken') }}">
                            <button type="button" class="btn btn-outline-secondary copy-icon"
                                onclick="copyRecoveryToken(this)"
                                style="border-color: var(--border-color); color: var(--primary-color);">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <p class="small text-center" style="color: var(--primary-color);">*Copie o código e cole na
                            sua **missão** para validar.</p>
                    </form>
                </div>
                <div class="modal-footer" style="border-top-color: var(--border-color);">
                    <button type="button" class="btn login_btn" onclick="recoverAccount(this)">
                        <span class="spinner-border spinner-border-sm d-none" role="status"
                            aria-hidden="true"></span>
                        <span class="btn-text">ALTERAR SENHA</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center footer-text position-fixed bottom-0 start-50 translate-middle-x p-2">
        <small>S.I.G.O. {{ date('Y') }} &copy; - SISTEMA INTEGRADO DE GERENCIAMENTO OPERACIONAL.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="{{ asset('js/login_tactical.js') }}"></script>

    <script>
        // CORREÇÃO 1: SCRIPT DO TEMA REMOVIDO DAQUI

        async function copyToken(buttonElement) {
            const tokenInput = document.getElementById('tokenInput');
            try {
                await navigator.clipboard.writeText(tokenInput.value);
                const icon = buttonElement.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                buttonElement.style.borderColor = '#198754';
                buttonElement.style.color = '#198754';

                // ATUALIZADO
                iziToast.success({
                    title: 'TRANSMISSÃO RECEBIDA',
                    message: 'Código de verificação copiado para a área de transferência.',
                    position: 'topRight',
                    icon: 'fas fa-check-circle',
                });

                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                    buttonElement.style.borderColor = '';
                    buttonElement.style.color = '';
                }, 2000);
            } catch (err) {
                iziToast.error({
                    title: 'FALHA NA OPERAÇÃO',
                    message: 'Não foi possível copiar o código.',
                    position: 'topRight',
                    icon: 'fas fa-times-circle',
                });
            }
        }

        async function copyRecoveryToken(buttonElement) {
            const tokenInput = document.getElementById('tokenInputRecovery');
            try {
                await navigator.clipboard.writeText(tokenInput.value);
                const icon = buttonElement.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                buttonElement.style.borderColor = '#27c93f';
                buttonElement.style.color = '#27c93f';
                iziToast.success({
                    title: 'TRANSMISSÃO RECEBIDA',
                    message: 'Código de recuperação copiado para a área de transferência.',
                    position: 'topRight',
                    icon: 'fas fa-check-circle',
                });

                setTimeout(() => {
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                    buttonElement.style.borderColor = '';
                    buttonElement.style.color = '';
                }, 2000);
            } catch (err) {
                // ATUALIZADO
                iziToast.error({
                    title: 'FALHA NA OPERAÇÃO',
                    message: 'Não foi possível copiar o código.',
                    position: 'topRight',
                    icon: 'fas fa-times-circle',
                });
            }
        }

        function createAccount(buttonElement) {
            const spinner = buttonElement.querySelector('.spinner-border');
            const btnText = buttonElement.querySelector('.btn-text');
            spinner.classList.remove('d-none');
            btnText.textContent = 'PROCESSANDO...';
            buttonElement.disabled = true;

            var formData = $('#formCreate').serialize();
            formData += '&_token={{ csrf_token() }}';

            $.ajax({
                type: "POST",
                url: "{{ route('login.register') }}",
                data: formData,
                success: function(response) {
                    if (response.success === true) {
                        iziToast.success({
                            title: 'REGISTRO EFETUADO',
                            message: 'Sua conta foi criada. Prossiga para autenticação.',
                            position: 'topRight',
                            icon: 'fas fa-user-plus',
                        });
                        const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                        modal.hide();
                    } else {
                        iziToast.error({
                            title: 'ACESSO NEGADO',
                            message: response.message ||
                                'Verifique os dados ou se o código foi colado na missão.',
                            position: 'topRight',
                            icon: 'fas fa-ban',
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '<ul>';
                    for (let field in errors) {
                        errorMessage += `<li>${errors[field][0]}</li>`;
                    }
                    errorMessage += '</ul>';
                    iziToast.error({
                        title: 'DADOS CORROMPIDOS',
                        message: errorMessage,
                        position: 'topRight',
                        icon: 'fas fa-exclamation-triangle',
                    });
                },
                complete: function() {
                    spinner.classList.add('d-none');
                    btnText.textContent = 'FINALIZAR REGISTRO';
                    buttonElement.disabled = false;
                }
            });
        }

        function recoverAccount(buttonElement) {
            const spinner = buttonElement.querySelector('.spinner-border');
            const btnText = buttonElement.querySelector('.btn-text');
            spinner.classList.remove('d-none');
            btnText.textContent = 'VALIDANDO...';
            buttonElement.disabled = true;

            var formData = $('#formRecovery').serialize();
            formData += '&_token={{ csrf_token() }}';

            $.ajax({
                type: "POST",
                url: "{{ route('login.recover') }}",
                data: formData,
                success: function(response) {
                    if (response.success === true) {
                        iziToast.success({
                            title: 'OPERAÇÃO CONCLUÍDA',
                            message: 'Sua senha foi alterada. Prossiga para autenticação.',
                            position: 'topRight',
                            icon: 'fas fa-shield-alt',
                        });
                        const modal = bootstrap.Modal.getInstance(document.getElementById('recoveryModal'));
                        modal.hide();
                    } else {
                        iziToast.error({
                            title: 'VALIDAÇÃO FALHOU',
                            message: response.message || 'Verifique o nickname e o código na missão.',
                            position: 'topRight',
                            icon: 'fas fa-ban',
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '<ul>';
                    for (let field in errors) {
                        errorMessage += `<li>${errors[field][0]}</li>`;
                    }
                    errorMessage += '</ul>';
                    iziToast.error({
                        title: 'DADOS CORROMPIDOS',
                        message: errorMessage,
                        position: 'topRight',
                        icon: 'fas fa-exclamation-triangle',
                    });
                },
                complete: function() {
                    spinner.classList.add('d-none');
                    btnText.textContent = 'ALTERAR SENHA';
                    buttonElement.disabled = false;
                }
            });
        }
    </script>
    @if (session('notification'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notification = @json(session('notification'));
                iziToast[notification.type]({
                    title: notification.title,
                    message: notification.message,
                    icon: notification.icon,
                    position: 'topRight'
                });
            });
        </script>
    @endif
</body>

</html>
