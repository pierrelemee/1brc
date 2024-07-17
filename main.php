<?php

class CityMeasurements
{
    protected string $name;
    protected int $nbMeasurements = 0;
    protected float $aggregated = 0.;
    protected float $min = PHP_FLOAT_MAX;
    protected float $max = PHP_FLOAT_MIN;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function process(float $measurement): void
    {
        $this->nbMeasurements++;
        $this->aggregated += $measurement;

        $this->min = min($this->min, $measurement);
        $this->max = max($this->max, $measurement);
    }

    public function getMean(): float {
        if ($this->nbMeasurements == 0) {
            return 0.;
        }

        return $this->aggregated / $this->nbMeasurements;
    }

    public function __toString(): string
    {
        return sprintf("$this->name=%.1f/%.1f/%.1f", $this->min, $this->getMean(), $this->max);
    }


}
/** @var CityMeasurements[] $cities */
$cities = [];

$fp = @fopen("./data/measurements.txt", "r");

if ($fp) {
    while (($line = fgets($fp, 4096)) !== false) {
        list($name, $measurement) = explode(";", $line);

        if (!isset($cities[$name])) {
            $cities[$name] = new CityMeasurements($name);
        }
        $cities[$name]->process(floatval($measurement));
    }

    ksort($cities);
    echo sprintf(
        "{%s}",
        join(
            ', ',
            array_map(
                fn(CityMeasurements $city) => $city->__toString(),
                $cities
            ),
        )
    );

    fclose($fp);
}
