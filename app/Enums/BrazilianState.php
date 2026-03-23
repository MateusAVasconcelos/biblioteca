<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BrazilianState: string implements HasLabel
{
    case Acre = 'AC';
    case Alagoas = 'AL';
    case Amapa = 'AP';
    case Amazonas = 'AM';
    case Bahia = 'BA';
    case Ceara = 'CE';
    case DistritoFederal = 'DF';
    case EspiritoSanto = 'ES';
    case Goias = 'GO';
    case Maranhao = 'MA';
    case MatoGrosso = 'MT';
    case MatoGrossoDoSul = 'MS';
    case MinasGerais = 'MG';
    case Para = 'PA';
    case Paraiba = 'PB';
    case Parana = 'PR';
    case Pernambuco = 'PE';
    case Piaui = 'PI';
    case RioDeJaneiro = 'RJ';
    case RioGrandeDoNorte = 'RN';
    case RioGrandeDoSul = 'RS';
    case Rondonia = 'RO';
    case Roraima = 'RR';
    case SantaCatarina = 'SC';
    case SaoPaulo = 'SP';
    case Sergipe = 'SE';
    case Tocantins = 'TO';

    public function getLabel(): string
    {
        return match ($this) {
            self::Acre => 'Acre',
            self::Alagoas => 'Alagoas',
            self::Amapa => 'Amapa',
            self::Amazonas => 'Amazonas',
            self::Bahia => 'Bahia',
            self::Ceara => 'Ceara',
            self::DistritoFederal => 'Distrito Federal',
            self::EspiritoSanto => 'Espirito Santo',
            self::Goias => 'Goias',
            self::Maranhao => 'Maranhao',
            self::MatoGrosso => 'Mato Grosso',
            self::MatoGrossoDoSul => 'Mato Grosso do Sul',
            self::MinasGerais => 'Minas Gerais',
            self::Para => 'Para',
            self::Paraiba => 'Paraiba',
            self::Parana => 'Parana',
            self::Pernambuco => 'Pernambuco',
            self::Piaui => 'Piaui',
            self::RioDeJaneiro => 'Rio de Janeiro',
            self::RioGrandeDoNorte => 'Rio Grande do Norte',
            self::RioGrandeDoSul => 'Rio Grande do Sul',
            self::Rondonia => 'Rondonia',
            self::Roraima => 'Roraima',
            self::SantaCatarina => 'Santa Catarina',
            self::SaoPaulo => 'Sao Paulo',
            self::Sergipe => 'Sergipe',
            self::Tocantins => 'Tocantins',
        };
    }

    /** @return array<string, string> */
    public static function toOptions(): array
    {
        return array_column(
            array_map(fn (self $state) => ['value' => $state->value, 'label' => $state->getLabel()], self::cases()),
            'label',
            'value',
        );
    }
}
