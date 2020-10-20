<?php
	class ContaBanco {
		private $numConta;
		private $tipo;
		private $dono;
		private $saldo;
		private $status;

		// Método construtor com parâmetros
		public function __construct ($nC="", $t="", $d="", $sa="", $st="") {
			if (!empty($nC)) {
				$this->setNumConta($nC);
				$this->setTipo($t);
				$this->setDono($d);
				$this->setSaldo($sa);
				$this->setStatus($st);
			} else {
				$this->setSaldo(0);
				$this->setStatus(false);
			}
		}

		// Getters e setters
		public function getNumConta () {
			return $this->numConta;
		}

		public function setNumConta ($numConta) {
			$this->numConta = $numConta;
		}

		public function getTipo () {
			return ($this->tipo=='cc'?0:1);
		}

		public function setTipo ($tipo) {
			$this->tipo = $tipo;
		}

		public function getDono () {
			return $this->dono;
		}

		public function setDono ($dono) {
			$this->dono = $dono;
		}

		public function getSaldo () {
			return $this->saldo;
		}

		public function setSaldo ($saldo) {
			$this->saldo = $saldo;
		}

		public function getStatus () {
			return $this->status;
		}

		public function setStatus ($status) {
			$this->status = $status;
		}

		// Métodos personalizados
		public function abrirConta ($tipo,
			$dono) {
			if ($this->getStatus()) {
				return '<p class="error">Impos. abrir conta.<br/>Detalhes: Conta já aberta.</p>';
			} else {
				$this->setTipo($tipo);
				$this->setDono($dono);
				if ($this->getTipo() === 0) {
					$this->setSaldo(50);
				} else {
					$this->setSaldo(150);
				}
				$this->setStatus(true);
			}
		}

		public function fecharConta () {
			if ($this->getSaldo() < 0) {
				return 0;
			} elseif ($this->getSaldo() > 0) {
				return 1;
			} elseif ($this->getSaldo() == 0) {
				$this->setNumConta(0);
				$this->setTipo('');
				$this->setDono('');
				$this->setStatus(false);
				return '<p class="success">Conta fechada com sucesso.</p>';
			}
		}

		public function depositar ($dep) {
			if ($this->getStatus()) {
				$this->setSaldo($this->getSaldo() + $dep);
				return '<p class="success">Depósito bem-sucedido. Saldo de R$'. 
					number_format($this->getSaldo(), 2, ',', '.'). '.</p>';
			} else {
				return '<p class="error">Depósito mal-sucedido.<br/>Detalhes: conta fechada.</p>';
			}
		}

		public function sacar ($saq) {
			if ($this->getStatus()) {
				if ($this->getSaldo() >= $saq) {
				$this->setSaldo($this->getSaldo() - $saq);
				return '<p class="success">Saque efetuado com êxito. Saldo atual: R$'. 
					number_format($this->getSaldo(), 2, ',', '.'). '.</p>';
				} else {
					return '<p class="error">Saque mal-sucedido.<br/>Detalhes: '.
						'saldo insuficiente.</p>';
				}
			} else {
				return '<p class="error">Saque mal-sucedido.<br/>Detalhes: conta fechada.</p>';
			}
		}

		public function pagarMensal ($men) {
			if ($this->getStatus()) {
				$saldo = $this->getSaldo();
				if ($saldo >= $men) {
					$this->setSaldo($saldo -= $men);
				} else {
					return 0;
				}
				return 1;
			}
		}
	}
?>