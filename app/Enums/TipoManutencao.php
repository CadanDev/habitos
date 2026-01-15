<?php
/**
 * Enum para tipos de manutenção de veículos
 */

enum TipoManutencao: string {
    // Lavadas
    case LAVADA_EXTERNA = 'lavada_externa';
    case LAVADA_INTERNA = 'lavada_interna';
    case LAVADA_COMPLETA = 'lavada_completa';
    
    // Trocas de Óleo
    case TROCA_OLEO_SIMPLES = 'troca_oleo_simples';
    case TROCA_OLEO_AVANCADA = 'troca_oleo_avancada';
    
    // Manutenções preventivas
    case REVISAO_FREIOS = 'revisao_freios';
    case REVISAO_RODAS = 'revisao_rodas';
    case REVISAO_SUSPENSAO = 'revisao_suspensao';
    case REVISAO_BATERIA = 'revisao_bateria';
    case TROCA_FILTRO_AR = 'troca_filtro_ar';
    case TROCA_FILTRO_OLEO = 'troca_filtro_oleo';
    case TROCA_FILTRO_COMBUSTIVEL = 'troca_filtro_combustivel';
    
    // Manutenções corretivas
    case MANUTENCAO_MOTOR = 'manutencao_motor';
    case MANUTENCAO_TRANSMISSAO = 'manutencao_transmissao';
    case MANUTENCAO_ELETRICA = 'manutencao_eletrica';
    case MANUTENCAO_SUSPENSAO_CORRETIVA = 'manutencao_suspensao_corretiva';
    
    // Emergências
    case EMERGENCIAL = 'emergencial';
    case REPARO_URGENTE = 'reparo_urgente';
    
    /**
     * Retorna o nome legível do tipo de manutenção
     */
    public function label(): string
    {
        return match($this) {
            self::LAVADA_EXTERNA => 'Lavada Externa',
            self::LAVADA_INTERNA => 'Lavada Interna',
            self::LAVADA_COMPLETA => 'Lavada Completa',
            self::TROCA_OLEO_SIMPLES => 'Troca de Óleo Simples',
            self::TROCA_OLEO_AVANCADA => 'Troca de Óleo Avançada',
            self::REVISAO_FREIOS => 'Revisão Freios',
            self::REVISAO_RODAS => 'Revisão Rodas',
            self::REVISAO_SUSPENSAO => 'Revisão Suspensão',
            self::REVISAO_BATERIA => 'Revisão Bateria',
            self::TROCA_FILTRO_AR => 'Troca Filtro Ar',
            self::TROCA_FILTRO_OLEO => 'Troca Filtro Óleo',
            self::TROCA_FILTRO_COMBUSTIVEL => 'Troca Filtro Combustível',
            self::MANUTENCAO_MOTOR => 'Manutenção Motor',
            self::MANUTENCAO_TRANSMISSAO => 'Manutenção Transmissão',
            self::MANUTENCAO_ELETRICA => 'Manutenção Elétrica',
            self::MANUTENCAO_SUSPENSAO_CORRETIVA => 'Manutenção Suspensão',
            self::EMERGENCIAL => 'Emergencial',
            self::REPARO_URGENTE => 'Reparo Urgente',
        };
    }
    
    /**
     * Retorna uma cor para cada tipo de manutenção
     */
    public function color(): string
    {
        return match($this) {
            self::LAVADA_EXTERNA, self::LAVADA_INTERNA, self::LAVADA_COMPLETA => '#3b82f6',
            self::TROCA_OLEO_SIMPLES, self::TROCA_OLEO_AVANCADA => '#10b981',
            self::REVISAO_FREIOS, self::REVISAO_RODAS, self::REVISAO_SUSPENSAO, self::REVISAO_BATERIA => '#f59e0b',
            self::TROCA_FILTRO_AR, self::TROCA_FILTRO_OLEO, self::TROCA_FILTRO_COMBUSTIVEL => '#06b6d4',
            self::MANUTENCAO_MOTOR, self::MANUTENCAO_TRANSMISSAO, self::MANUTENCAO_ELETRICA, self::MANUTENCAO_SUSPENSAO_CORRETIVA => '#8b5cf6',
            self::EMERGENCIAL, self::REPARO_URGENTE => '#ef4444',
        };
    }
    
    /**
     * Retorna todos os tipos de manutenção agrupados por categoria
     */
    public static function grouped(): array
    {
        return [
            'Lavadas' => [
                self::LAVADA_EXTERNA,
                self::LAVADA_INTERNA,
                self::LAVADA_COMPLETA,
            ],
            'Trocas de Óleo' => [
                self::TROCA_OLEO_SIMPLES,
                self::TROCA_OLEO_AVANCADA,
            ],
            'Revisões' => [
                self::REVISAO_FREIOS,
                self::REVISAO_RODAS,
                self::REVISAO_SUSPENSAO,
                self::REVISAO_BATERIA,
                self::TROCA_FILTRO_AR,
                self::TROCA_FILTRO_OLEO,
                self::TROCA_FILTRO_COMBUSTIVEL,
            ],
            'Manutenções Corretivas' => [
                self::MANUTENCAO_MOTOR,
                self::MANUTENCAO_TRANSMISSAO,
                self::MANUTENCAO_ELETRICA,
                self::MANUTENCAO_SUSPENSAO_CORRETIVA,
            ],
            'Emergências' => [
                self::EMERGENCIAL,
                self::REPARO_URGENTE,
            ],
        ];
    }
}
