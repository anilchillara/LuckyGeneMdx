/**
 * LuckyGeneMDx - Main JavaScript
 * Handles animations, interactions, and form validation
 */

(function() {
    'use strict';

    // ============================================
    // INITIALIZATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initNavigation();
        initScrollAnimations();
        initParticles();
        initFormValidation();
        initCarousel();
        initSmoothScroll();
    });

    // ============================================
    // NAVIGATION
    // ============================================
    function initNavigation() {
        const navbar = document.querySelector('.navbar');
        const navbarToggle = document.querySelector('.navbar-toggle');
        const navbarMenu = document.querySelector('.navbar-menu');
        
        // Scroll effect on navbar
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar?.classList.add('scrolled');
            } else {
                navbar?.classList.remove('scrolled');
            }
        });
        
        // Mobile menu toggle
        navbarToggle?.addEventListener('click', function() {
            navbarMenu?.classList.toggle('active');
            this.textContent = navbarMenu?.classList.contains('active') ? '✕' : '☰';
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar') && navbarMenu?.classList.contains('active')) {
                navbarMenu.classList.remove('active');
                if (navbarToggle) navbarToggle.textContent = '☰';
            }
        });
    }

    // ============================================
    // SCROLL ANIMATIONS
    // ============================================
    function initScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observe elements with fade-in class
        document.querySelectorAll('.fade-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(el);
        });
        
        // Staggered animations
        document.querySelectorAll('.stagger-animation').forEach((parent, parentIndex) => {
            const children = parent.children;
            Array.from(children).forEach((child, index) => {
                child.style.opacity = '0';
                child.style.transform = 'translateY(20px)';
                child.style.transition = `opacity 0.6s ease-out ${index * 0.1}s, transform 0.6s ease-out ${index * 0.1}s`;
                observer.observe(child);
            });
        });
    }

    // ============================================
    // PARTICLE BACKGROUND
    // ============================================
    function initParticles() {
        const particlesContainer = document.querySelector('.particles');
        if (!particlesContainer) return;
        
        const particleCount = 30;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Random size between 2-8px
            const size = Math.random() * 6 + 2;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            
            // Random starting position
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            
            // Random animation duration between 60-90s
            const duration = Math.random() * 30 + 60;
            particle.style.animationDuration = duration + 's';
            
            // Random animation delay
            particle.style.animationDelay = Math.random() * 20 + 's';
            
            particlesContainer.appendChild(particle);
        }
    }

    // ============================================
    // FORM VALIDATION
    // ============================================
    function initFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(form)) {
                    e.preventDefault();
                }
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(input);
                });
                
                input.addEventListener('input', function() {
                    if (input.classList.contains('error')) {
                        validateField(input);
                    }
                });
            });
        });
    }

    function validateForm(form) {
        const inputs = form.querySelectorAll('[required], [data-validate]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    function validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const validationType = field.getAttribute('data-validate');
        let isValid = true;
        let errorMessage = '';
        
        // Required field check
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }
        
        // Email validation
        else if (type === 'email' || validationType === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value && !emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }
        
        // Phone validation
        else if (validationType === 'phone') {
            const phoneRegex = /^[\d\s\-\+\(\)]{10,}$/;
            if (value && !phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }
        
        // Date validation
        else if (type === 'date') {
            const dateValue = new Date(value);
            const today = new Date();
            if (dateValue > today) {
                isValid = false;
                errorMessage = 'Date cannot be in the future';
            }
        }
        
        // Password strength
        else if (validationType === 'password' && value) {
            if (value.length < 8) {
                isValid = false;
                errorMessage = 'Password must be at least 8 characters';
            }
        }
        
        // Confirm password
        else if (validationType === 'confirm-password') {
            const passwordField = document.querySelector('[data-validate="password"]');
            if (passwordField && value !== passwordField.value) {
                isValid = false;
                errorMessage = 'Passwords do not match';
            }
        }
        
        // Display validation state
        showValidationState(field, isValid, errorMessage);
        
        return isValid;
    }

    function showValidationState(field, isValid, errorMessage) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;
        
        // Remove existing error message
        const existingError = formGroup.querySelector('.form-error');
        if (existingError) {
            existingError.remove();
        }
        
        if (isValid) {
            field.classList.remove('error');
            field.classList.add('success');
        } else {
            field.classList.remove('success');
            field.classList.add('error');
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            errorDiv.textContent = errorMessage;
            formGroup.appendChild(errorDiv);
        }
    }

    // ============================================
    // CAROUSEL / TESTIMONIALS
    // ============================================
    function initCarousel() {
        const carousel = document.querySelector('.testimonials-carousel');
        if (!carousel) return;
        
        const items = carousel.querySelectorAll('.testimonial-item');
        if (items.length === 0) return;
        
        let currentIndex = 0;
        const intervalTime = 5000;
        
        // Hide all items except first
        items.forEach((item, index) => {
            item.style.display = index === 0 ? 'block' : 'none';
        });
        
        function showSlide(index) {
            items.forEach((item, i) => {
                item.style.display = i === index ? 'block' : 'none';
                item.style.animation = i === index ? 'fadeIn 0.5s ease-in' : 'none';
            });
        }
        
        function nextSlide() {
            currentIndex = (currentIndex + 1) % items.length;
            showSlide(currentIndex);
        }
        
        function prevSlide() {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            showSlide(currentIndex);
        }
        
        // Auto-advance
        let autoPlay = setInterval(nextSlide, intervalTime);
        
        // Pause on hover
        carousel.addEventListener('mouseenter', () => {
            clearInterval(autoPlay);
        });
        
        carousel.addEventListener('mouseleave', () => {
            autoPlay = setInterval(nextSlide, intervalTime);
        });
        
        // Navigation buttons
        const prevBtn = carousel.querySelector('.carousel-prev');
        const nextBtn = carousel.querySelector('.carousel-next');
        
        prevBtn?.addEventListener('click', () => {
            clearInterval(autoPlay);
            prevSlide();
            autoPlay = setInterval(nextSlide, intervalTime);
        });
        
        nextBtn?.addEventListener('click', () => {
            clearInterval(autoPlay);
            nextSlide();
            autoPlay = setInterval(nextSlide, intervalTime);
        });
    }

    // ============================================
    // SMOOTH SCROLL
    // ============================================
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                e.preventDefault();
                const target = document.querySelector(href);
                
                if (target) {
                    const navHeight = document.querySelector('.navbar')?.offsetHeight || 0;
                    const targetPosition = target.offsetTop - navHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ============================================
    // LOADING STATES
    // ============================================
    window.showLoading = function(button) {
        button.disabled = true;
        button.setAttribute('data-original-text', button.textContent);
        button.innerHTML = '<span class="spinner"></span> Processing...';
    };

    window.hideLoading = function(button) {
        button.disabled = false;
        button.textContent = button.getAttribute('data-original-text') || 'Submit';
    };

    // ============================================
    // CSRF TOKEN HELPER
    // ============================================
    window.getCSRFToken = function() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    };

    // ============================================
    // AJAX REQUEST HELPER
    // ============================================
    window.makeRequest = async function(url, method = 'POST', data = {}) {
        try {
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCSRFToken()
                }
            };
            
            if (method !== 'GET' && data) {
                options.body = JSON.stringify(data);
            }
            
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            return { success: false, message: 'An error occurred. Please try again.' };
        }
    };

    // ============================================
    // NOTIFICATION SYSTEM
    // ============================================
    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    };

    // ============================================
    // DNA HELIX SVG GENERATION
    // ============================================
    window.generateDNAHelix = function(containerId) {
        const container = document.getElementById(containerId);
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('viewBox', '0 0 200 600');
        svg.setAttribute('class', 'dna-svg');
        
        const config = {
            totalNodes: 35,        // Dense nodes create the "one strand" effect
            baseSpeed: 0.015,
            amplitude: 55,         // Width of the twist
            verticalSpacing: 16,   // Tight vertical gap for cohesion
            teal: '#00B3A4',
            purple: '#6C63FF'
        };
    
        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        defs.innerHTML = `
            <filter id="glossGlow" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="2.5" result="blur"/>
                <feComposite in="SourceGraphic" in2="blur" operator="over"/>
            </filter>
            
            <radialGradient id="sphereTeal" cx="30%" cy="30%" r="50%">
                <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.9"/>
                <stop offset="40%" stop-color="${config.teal}" stop-opacity="1"/>
                <stop offset="100%" stop-color="#0A1F44" stop-opacity="1"/>
            </radialGradient>
    
            <linearGradient id="rungGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#0A1F44" stop-opacity="0.2"/>
                <stop offset="100%" stop-color="${config.purple}" stop-opacity="0.2"/>
            </linearGradient>
        `;
        svg.appendChild(defs);
    
        const nodes = Array.from({ length: config.totalNodes }, (_, i) => {
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            const rung = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            const dot1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            const dot2 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            
            rung.setAttribute('stroke', 'url(#rungGrad)');
            rung.setAttribute('stroke-width', '1');
    
            [dot1, dot2].forEach((dot, idx) => {
                // Apply glossy gradient
                dot.setAttribute('fill', 'url(#sphereTeal)');
                dot.setAttribute('filter', 'url(#glossGlow)');
                // Add a subtle purple stroke to one side for medical richness
                if(idx === 1) dot.setAttribute('stroke', config.purple);
                dot.setAttribute('stroke-width', '0.5');
                g.appendChild(dot);
            });
            
            g.insertBefore(rung, g.firstChild);
            svg.appendChild(g);
            
            // This specific offset (0.18) forces the single unified spiral
            return { g, rung, dot1, dot2, offset: i * 0.18 };
        });
    
        container.appendChild(svg);
    
        let tick = 0;
        function animate() {
            tick += config.baseSpeed;
            nodes.forEach((n, i) => {
                const y = 30 + (i * config.verticalSpacing);
                const angle = tick + n.offset;
                
                const x1 = 100 + Math.sin(angle) * config.amplitude;
                const x2 = 100 + Math.sin(angle + Math.PI) * config.amplitude;
                const z = Math.cos(angle); 
                
                // 3D Depth Scaling
                const r1 = 4.5 + (z * 2.5);
                const r2 = 4.5 + (-z * 2.5);
    
                n.rung.setAttribute('x1', x1);
                n.rung.setAttribute('y1', y);
                n.rung.setAttribute('x2', x2);
                n.rung.setAttribute('y2', y);
                n.rung.setAttribute('opacity', Math.max(0.1, Math.abs(z) * 0.4));
                
                n.dot1.setAttribute('cx', x1); n.dot1.setAttribute('cy', y); n.dot1.setAttribute('r', r1);
                n.dot2.setAttribute('cx', x2); n.dot2.setAttribute('cy', y); n.dot2.setAttribute('r', r2);
    
                // Z-Ordering logic to keep it as one solid object
                if (z > 0) n.g.appendChild(n.dot1); 
                else n.g.appendChild(n.dot2);
            });
            requestAnimationFrame(animate);
        }
        animate();
    };

    // ============================================
    // EXPORT FOR GLOBAL USE
    // ============================================
    window.LuckyGeneMDx = {
        validateForm,
        validateField,
        showLoading,
        hideLoading,
        showNotification,
        makeRequest,
        generateDNAHelix
    };

})();

// CSS for animations (add to style tag if needed)
const styleSheet = document.createElement('style');
styleSheet.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid #ffffff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(styleSheet);
