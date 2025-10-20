document.addEventListener('DOMContentLoaded', function() {
    particlesJS('particles-js', {
        "particles": {
            "number": {
                "value": 100, // Densidade de partículas
                "density": {
                    "enable": true,
                    "value_area": 800
                }
            },
            "color": {
                "value": "#00ff7f" // Cor das partículas
            },
            "shape": {
                "type": "circle",
            },
            "opacity": {
                "value": 0.5,
                "random": true,
            },
            "size": {
                "value": 2,
                "random": true,
            },
            "line_linked": {
                "enable": true,
                "distance": 150,
                "color": "#00ff7f", // Cor das linhas
                "opacity": 0.2,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 2, // Velocidade das partículas
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "grab" // Efeito ao passar o mouse
                },
                "onclick": {
                    "enable": true,
                    "mode": "push" // Efeito ao clicar
                },
                "resize": true
            },
            "modes": {
                "grab": {
                    "distance": 140,
                    "line_opacity": 0.5
                },
                "bubble": {
                    "distance": 400,
                    "size": 40,
                    "duration": 2,
                    "opacity": 8,
                    "speed": 3
                },
                "repulse": {
                    "distance": 200,
                    "duration": 0.4
                },
                "push": {
                    "particles_nb": 4
                },
                "remove": {
                    "particles_nb": 2
                }
            }
        },
        "retina_detect": true
    });
});
