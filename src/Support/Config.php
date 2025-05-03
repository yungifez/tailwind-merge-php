<?php

namespace TailwindMerge\Support;

use BcMath\Number;
use TailwindMerge\Validators\AnyNonArbitraryValidator;
use TailwindMerge\Validators\AnyValueValidator;
use TailwindMerge\Validators\ArbitraryImageValidator;
use TailwindMerge\Validators\ArbitraryLengthValidator;
use TailwindMerge\Validators\ArbitraryNumberValidator;
use TailwindMerge\Validators\ArbitraryPositionValidator;
use TailwindMerge\Validators\ArbitraryShadowValidator;
use TailwindMerge\Validators\ArbitrarySizeValidator;
use TailwindMerge\Validators\ArbitraryValueValidator;
use TailwindMerge\Validators\ArbitraryVariable;
use TailwindMerge\Validators\ArbitraryVariableFamilyNameValidator;
use TailwindMerge\Validators\ArbitraryVariableImageValidator;
use TailwindMerge\Validators\ArbitraryVariableLengthValidator;
use TailwindMerge\Validators\ArbitraryVariablePositionValidator;
use TailwindMerge\Validators\ArbitraryVariableShadowValidator;
use TailwindMerge\Validators\ArbitraryVariableSizeValidator;
use TailwindMerge\Validators\ArbitraryVariableValidator;
use TailwindMerge\Validators\FractionValidator;
use TailwindMerge\Validators\IntegerValidator;
use TailwindMerge\Validators\LengthValidator;
use TailwindMerge\Validators\NumberValidator;
use TailwindMerge\Validators\PercentValidator;
use TailwindMerge\Validators\TshirtSizeValidator;
use TailwindMerge\ValueObjects\ThemeGetter;
use phpDocumentor\Reflection\Types\Integer;

class Config
{
    /**
     * @var array<string, mixed>
     */
    private static array $additionalConfig = [];

    /**
     * @return array<string, mixed>
     */
    public static function getMergedConfig(): array
    {
        $config = self::getDefaultConfig();

        foreach (self::$additionalConfig as $key => $additionalConfig) {
            $config[$key] = self::mergePropertyRecursively($config, $key, $additionalConfig);
        }

        return $config;
    }

    private static function mergePropertyRecursively(array $baseConfig, string $mergeKey, array|bool|float|int|string|null $mergeValue): array|bool|float|int|string|null
    {
        if (! array_key_exists($mergeKey, $baseConfig)) {
            return $mergeValue;
        }
        if (is_string($mergeValue)) {
            return $mergeValue;
        }
        if (is_numeric($mergeValue)) {
            return $mergeValue;
        }
        if (is_bool($mergeValue)) {
            return $mergeValue;
        }
        if ($mergeValue === null) {
            return $mergeValue;
        }
        if (is_array($mergeValue) && array_is_list($mergeValue) && is_array($baseConfig[$mergeKey]) && array_is_list($baseConfig[$mergeKey])) {
            return [...$baseConfig[$mergeKey], ...$mergeValue];
        }

        if (is_array($mergeValue) && ! array_is_list($mergeValue) /* && is_array($baseConfig[$mergeKey]) && array_is_list($baseConfig[$mergeKey]) */) {
            if ($baseConfig[$mergeKey] === null) {
                return $mergeValue;
            }

            foreach ($mergeValue as $key => $value) {
                $baseConfig[$mergeKey][$key] = self::mergePropertyRecursively($baseConfig[$mergeKey], $key, $value);
            }
        }

        return $baseConfig[$mergeKey];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function setAdditionalConfig(array $config): void
    {
        self::$additionalConfig = $config;
    }

    /**
     * @return array{cacheSize: int, prefix: ?string, theme: array<string, mixed>, classGroups: array<string, mixed>,conflictingClassGroups: array<string, array<int, string>>, conflictingClassGroupModifiers: array<string, array<int, string>>}
     */
    public static function getDefaultConfig(): array
    {
        $color = self::fromTheme('color');
        $font = self::fromTheme('font');
        $text = self::fromTheme('text');
        $fontWeight = self::fromTheme('font-weight');
        $tracking = self::fromTheme('tracking');
        $leading = self::fromTheme('leading');
        $breakpoint = self::fromTheme('breakpoint');
        $container = self::fromTheme('container');
        $spacing = self::fromTheme('spacing');
        $radius = self::fromTheme('radius');
        $shadow = self::fromTheme('shadow');
        $insetShadow = self::fromTheme('inset-shadow');
        $dropShadow = self::fromTheme('drop-shadow');
        $blur = self::fromTheme('blur');
        $perspective = self::fromTheme('perspective');
        $aspect = self::fromTheme('aspect');
        $ease = self::fromTheme('ease');
        $animate = self::fromTheme('animate');

        return [
            'cacheSize' => 500,
            'prefix' => null,
            'theme' => [
                'color' => [AnyValueValidator::validate(...)],
                'font' => [AnyNonArbitraryValidator::validate(...), ArbitraryVariableFamilyNameValidator::validate(...), ArbitraryValueValidator::validate(...)],
                'text' => ['base', TshirtSizeValidator::validate(...), ArbitraryVariableLengthValidator::class, ArbitraryLengthValidator::validate(...)],
                'font-weight' => [
                    'thin',
                    'extralight',
                    'light',
                    'normal',
                    'medium',
                    'semibold',
                    'bold',
                    'extrabold',
                    'black',
                ],
                'tracking' => ['tighter', 'tight', 'normal', 'wide', 'wider', 'widest'],
                'leading' => ['none', 'tight', 'snug', 'normal', 'relaxed', 'loose'],
                'breakpoint' => [TshirtSizeValidator::validate(...)],
                'container' => [TshirtSizeValidator::validate(...)],
                'spacing' => [NumberValidator::validate(...), ArbitraryVariableLengthValidator::validate(...), ArbitraryLengthValidator::validate(...)],
                'radius' => [
                // Deprecated since Tailwind CSS v4.0.0
                    '',
                    TshirtSizeValidator::validate(...),
                    'none',
                    'full',
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...)
                ],
                'shadow' => [TshirtSizeValidator::validate(...), 'none', ArbitraryVariableShadowValidator::validate(...), ArbitraryShadowValidator::validate(...)],
                'inset-shadow' => [TshirtSizeValidator::validate(...)],
                'drop-shadow' => [
                    // Deprecated since Tailwind CSS v4.0.0
                    '',
                    TshirtSizeValidator::validate(...),
                    'none',
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ],
                'blur' => [
                    // Deprecated since Tailwind CSS v4.0.0
                    '',
                    TshirtSizeValidator::validate(...),
                    'none',
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...)
                ],
                'perspective' => ['dramatic', 'near', 'normal', 'midrange', 'distant', 'none'],
                'aspect' => ['video'],
                'ease' => ['in', 'out', 'in-out'],
                'animate' => ['spin', 'ping', 'pulse', 'bounce'],
            ],
            'classGroups' => [
                // --------------
                // --- Layout ---
                // --------------
                /**
                 * Aspect Ratio
                 *
                 * @see https://tailwindcss.com/docs/aspect-ratio
                 */
                'aspect' => [
                    [
                        'aspect' => [
                            'auto',
                            'square',
                            FractionValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            $aspect,
                        ],
                    ],
                ],
                /**
                 * Container
                 *
                 * @see https://tailwindcss.com/docs/container
                 * @deprecated since Tailwind CSS v4.0.0
                 */
                'container' => ['container'],
                /**
                 * Columns
                 *
                 * @see https://tailwindcss.com/docs/columns
                 */
                'columns' => [
                    [
                        'columns' => [
                            NumberValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                            $container,
                        ],
                    ],
                ],
                /**
                 * Break After
                 *
                 * @see https://tailwindcss.com/docs/break-after
                 */
                'break-after' => [['break-after' => self::getBreakScale()]],
                /**
                 * Break Before
                 *
                 * @see https://tailwindcss.com/docs/break-before
                 */
                'break-before' => [['break-before' => self::getBreakScale()]],
                /**
                 * Break Inside
                 *
                 * @see https://tailwindcss.com/docs/break-inside
                 */
                'break-inside' => [['break-inside' => ['auto', 'avoid', 'avoid-page', 'avoid-column']]],
                /**
                 * Box Decoration Break
                 *
                 * @see https://tailwindcss.com/docs/box-decoration-break
                 */
                'box-decoration' => [['box-decoration' => ['slice', 'clone']]],
                /**
                 * Box Sizing
                 *
                 * @see https://tailwindcss.com/docs/box-sizing
                 */
                'box' => [['box' => ['border', 'content']]],
                /**
                 * Display
                 *
                 * @see https://tailwindcss.com/docs/display
                 */
                'display' => [
                    'block',
                    'inline-block',
                    'inline',
                    'flex',
                    'inline-flex',
                    'table',
                    'inline-table',
                    'table-caption',
                    'table-cell',
                    'table-column',
                    'table-column-group',
                    'table-footer-group',
                    'table-header-group',
                    'table-row-group',
                    'table-row',
                    'flow-root',
                    'grid',
                    'inline-grid',
                    'contents',
                    'list-item',
                    'hidden',
                ],
                /**
                 * Screen Reader Only
                 * @see https://tailwindcss.com/docs/display#screen-reader-only
                 */
                'sr' => ['sr-only', 'not-sr-only'],
                /**
                 * Floats
                 *
                 * @see https://tailwindcss.com/docs/float
                 */
                'float' => [['float' => ['right', 'left', 'none', 'start', 'end']]],
                /**
                 * Clear
                 *
                 * @see https://tailwindcss.com/docs/clear
                 */
                'clear' => [['clear' => ['left', 'right', 'both', 'none', 'start', 'end']]],
                /**
                 * Isolation
                 *
                 * @see https://tailwindcss.com/docs/isolation
                 */
                'isolation' => ['isolate', 'isolation-auto'],
                /**
                 * Object Fit
                 *
                 * @see https://tailwindcss.com/docs/object-fit
                 */
                'object-fit' => [['object' => ['contain', 'cover', 'fill', 'none', 'scale-down']]],
                /**
                 * Object Position
                 *
                 * @see https://tailwindcss.com/docs/object-position
                 */
                'object-position' => [
                    [
                        'object' => [
                            ...self::getPositionScale(),
                            ArbitraryValueValidator::validate(...),
                            ArbitraryVariableValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Overflow
                 *
                 * @see https://tailwindcss.com/docs/overflow
                 */
                'overflow' => [['overflow' => self::getOverflowScale()]],
                /**
                 * Overflow X
                 *
                 * @see https://tailwindcss.com/docs/overflow
                 */
                'overflow-x' => [['overflow-x' => self::getOverflowScale()]],
                /**
                 * Overflow Y
                 *
                 * @see https://tailwindcss.com/docs/overflow
                 */
                'overflow-y' => [['overflow-y' => self::getOverflowScale()]],
                /**
                 * Overscroll Behavior
                 *
                 * @see https://tailwindcss.com/docs/overscroll-behavior
                 */
                'overscroll' => [['overscroll' => self::getOverscrollScale()]],
                /**
                 * Overscroll Behavior X
                 *
                 * @see https://tailwindcss.com/docs/overscroll-behavior
                 */
                'overscroll-x' => [['overscroll-x' => self::getOverscrollScale()]],
                /**
                 * Overscroll Behavior Y
                 *
                 * @see https://tailwindcss.com/docs/overscroll-behavior
                 */
                'overscroll-y' => [['overscroll-y' => self::getOverscrollScale()]],
                /**
                 * Position
                 *
                 * @see https://tailwindcss.com/docs/position
                 */
                'position' => ['static', 'fixed', 'absolute', 'relative', 'sticky'],
                /**
                 * Top / Right / Bottom / Left
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'inset' => [['inset' => self::getInsetScale($spacing)]],
                /**
                 * Right / Left
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'inset-x' => [['inset-x' => self::getInsetScale($spacing)]],
                /**
                 * Top / Bottom
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'inset-y' => [['inset-y' => self::getInsetScale($spacing)]],
                /**
                 * Start
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'start' => [['start' => self::getInsetScale($spacing)]],
                /**
                 * End
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'end' => [['end' => self::getInsetScale($spacing)]],
                /**
                 * Top
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'top' => [['top' => self::getInsetScale($spacing)]],
                /**
                 * Right
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'right' => [['right' => self::getInsetScale($spacing)]],
                /**
                 * Bottom
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'bottom' => [['bottom' => self::getInsetScale($spacing)]],
                /**
                 * Left
                 *
                 * @see https://tailwindcss.com/docs/top-right-bottom-left
                 */
                'left' => [['left' => self::getInsetScale($spacing)]],
                /**
                 * Visibility
                 *
                 * @see https://tailwindcss.com/docs/visibility
                 */
                'visibility' => ['visible', 'invisible', 'collapse'],
                /**
                 * Z-Index
                 *
                 * @see https://tailwindcss.com/docs/z-index
                 */
                'z' => [['z' => [IntegerValidator::validate(...), 'auto', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                // ------------------------
                // --- Flexbox and Grid ---
                // ------------------------
                /**
                 * Flex Basis
                 *
                 * @see https://tailwindcss.com/docs/flex-basis
                 */
                'basis' => [
                    [
                        'basis' => [
                            FractionValidator::validate(...),
                            'full',
                            'auto',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            $container,
                            $spacing,
                        ],
                    ],
                ],
                /**
                 * Flex Direction
                 *
                 * @see https://tailwindcss.com/docs/flex-direction
                 */
                'flex-direction' => [['flex' => ['row', 'row-reverse', 'col', 'col-reverse']]],
                /**
                 * Flex Wrap
                 *
                 * @see https://tailwindcss.com/docs/flex-wrap
                 */
                'flex-wrap' => [['flex' => ['nowrap', 'wrap', 'wrap-reverse']]],
                /**
                 * Flex
                 *
                 * @see https://tailwindcss.com/docs/flex
                 */
                'flex' => [['flex' => [NumberValidator::validate(...), FractionValidator::validate(...), 'auto', 'initial', 'none', ArbitraryValueValidator::validate(...)]]],
                /**
                 * Flex Grow
                 *
                 * @see https://tailwindcss.com/docs/flex-grow
                 */
                'grow' => [['grow' => ['', NumberValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * Flex Shrink
                 *
                 * @see https://tailwindcss.com/docs/flex-shrink
                 */
                'shrink' => [['shrink' => ['', NumberValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * Order
                 *
                 * @see https://tailwindcss.com/docs/order
                 */
                'order' => [
                    [
                        'order' => [
                            IntegerValidator::validate(...),
                            'first',
                            'last',
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Grid Template Columns
                 *
                 * @see https://tailwindcss.com/docs/grid-template-columns
                 */
                'grid-cols' => [['grid-cols' => self::getGridTemplateColsRowsScale()]],
                /**
                 * Grid Column Start / End
                 *
                 * @see https://tailwindcss.com/docs/grid-column
                 */
                'col-start-end' => [['col' => self::getGridColRowStartAndEndScale()]],
                /**
                 * Grid Column Start
                 *
                 * @see https://tailwindcss.com/docs/grid-column
                 */
                'col-start' => [['col-start' => self::getGridColRowStartOrEndScale()]],
                /**
                 * Grid Column End
                 *
                 * @see https://tailwindcss.com/docs/grid-column
                 */
                'col-end' => [['col-end' => self::getGridColRowStartOrEndScale()]],
                /**
                 * Grid Template Rows
                 *
                 * @see https://tailwindcss.com/docs/grid-template-rows
                 */
                'grid-rows' => [['grid-rows' => self::getGridTemplateColsRowsScale()]],
                /**
                 * Grid Row Start / End
                 *
                 * @see https://tailwindcss.com/docs/grid-row
                 */
                'row-start-end' => [['row' => self::getGridColRowStartAndEndScale() ]],
                /**
                 * Grid Row Start
                 *
                 * @see https://tailwindcss.com/docs/grid-row
                 */
                'row-start' => [['row-start' => self::getGridColRowStartOrEndScale()]],
                /**
                 * Grid Row End
                 *
                 * @see https://tailwindcss.com/docs/grid-row
                 */
                'row-end' => [['row-end' => self::getGridColRowStartOrEndScale()]],
                /**
                 * Grid Auto Flow
                 *
                 * @see https://tailwindcss.com/docs/grid-auto-flow
                 */
                'grid-flow' => [['grid-flow' => ['row', 'col', 'dense', 'row-dense', 'col-dense']]],
                /**
                 * Grid Auto Columns
                 *
                 * @see https://tailwindcss.com/docs/grid-auto-columns
                 */
                'auto-cols' => [['auto-cols' => self::getGridAutoColsRowsScale()]],
                /**
                 * Grid Auto Rows
                 *
                 * @see https://tailwindcss.com/docs/grid-auto-rows
                 */
                'auto-rows' => [['auto-rows' => self::getGridAutoColsRowsScale()]],
                /**
                 * Gap
                 *
                 * @see https://tailwindcss.com/docs/gap
                 */
                'gap' => [['gap' => self::getGapScale($spacing)]],
                /**
                 * Gap X
                 *
                 * @see https://tailwindcss.com/docs/gap
                 */
                'gap-x' => [['gap-x' => self::getGapScale($spacing)]],
                /**
                 * Gap Y
                 *
                 * @see https://tailwindcss.com/docs/gap
                 */
                'gap-y' => [['gap-y' => self::getGapScale($spacing)]],
                /**
                 * Justify Content
                 *
                 * @see https://tailwindcss.com/docs/justify-content
                 */
                'justify-content' => [['justify' => [...self::getAlignPrimaryAxisScale(), 'normal']]],
                /**
                 * Justify Items
                 *
                 * @see https://tailwindcss.com/docs/justify-items
                 */
                'justify-items' => [['justify-items' => [...self::getAlignSecondaryAxisScale(), 'normal']]],
                /**
                 * Justify Self
                 *
                 * @see https://tailwindcss.com/docs/justify-self
                 */
                'justify-self' => [['justify-self' => ['auto', ...self::getAlignSecondaryAxisScale()]]],
                /**
                 * Align Content
                 *
                 * @see https://tailwindcss.com/docs/align-content
                 */
                'align-content' => [['content' => ['normal', ...self::getAlignPrimaryAxisScale()]]],
                /**
                 * Align Items
                 *
                 * @see https://tailwindcss.com/docs/align-items
                 */
                'align-items' => [['items' => [...self::getAlignSecondaryAxisScale(), 'baseline']]],
                /**
                 * Align Self
                 *
                 * @see https://tailwindcss.com/docs/align-self
                 */
                'align-self' => [['self' => ['auto', ...self::getAlignSecondaryAxisScale(), 'baseline']]],
                /**
                 * Place Content
                 *
                 * @see https://tailwindcss.com/docs/place-content
                 */
                'place-content' => [['place-content' => self::getAlignPrimaryAxisScale()]],
                /**
                 * Place Items
                 *
                 * @see https://tailwindcss.com/docs/place-items
                 */
                'place-items' => [['place-items' => [...self::getAlignSecondaryAxisScale(), 'baseline']]],
                /**
                 * Place Self
                 *
                 * @see https://tailwindcss.com/docs/place-self
                 */
                'place-self' => [['place-self' => ['auto', ...self::getAlignSecondaryAxisScale()]]],
                // Spacing
                /**
                 * Padding
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'p' => [['p' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding X
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'px' => [['px' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding Y
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'py' => [['py' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding Start
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'ps' => [['ps' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding End
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pe' => [['pe' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding Top
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pt' => [['pt' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding Right
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pr' => [['pr' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding Bottom
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pb' => [['pb' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Padding Left
                 *
                 * @see https://tailwindcss.com/docs/padding
                 */
                'pl' => [['pl' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Margin
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'm' => [['m' => self::getMarginScale($spacing)]],
                /**
                 * Margin X
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mx' => [['mx' => self::getMarginScale($spacing)]],
                /**
                 * Margin Y
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'my' => [['my' => self::getMarginScale($spacing)]],
                /**
                 * Margin Start
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'ms' => [['ms' => self::getMarginScale($spacing)]],
                /**
                 * Margin End
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'me' => [['me' => self::getMarginScale($spacing)]],
                /**
                 * Margin Top
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mt' => [['mt' => self::getMarginScale($spacing)]],
                /**
                 * Margin Right
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mr' => [['mr' => self::getMarginScale($spacing)]],
                /**
                 * Margin Bottom
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'mb' => [['mb' => self::getMarginScale($spacing)]],
                /**
                 * Margin Left
                 *
                 * @see https://tailwindcss.com/docs/margin
                 */
                'ml' => [['ml' => self::getMarginScale($spacing)]],
                /**
                 * Space Between X
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                */
                'space-x' => [['space-x' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Space Between X Reverse
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                */
                'space-x-reverse' => ['space-x-reverse'],
                /**
                 * Space Between Y
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                 */
                'space-y' => [['space-y' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Space Between Y Reverse
                 *
                 * @see https://tailwindcss.com/docs/margin#adding-space-between-children
                */
                'space-y-reverse' => ['space-y-reverse'],

                // --------------
                // --- Sizing ---
                // --------------

                /**
                 * Size
                 * @see https://tailwindcss.com/docs/width#setting-both-width-and-height
                 */
                'size' => [[ 'size' => self::getSizingScale($spacing) ]],
                /**
                 * Width
                 *
                 * @see https://tailwindcss.com/docs/width
                 */
                'w' => [[ 'w' =>  [$container, 'screen', ...self::getSizingScale($spacing)] ]],
                /**
                 * Min-Width
                 *
                 * @see https://tailwindcss.com/docs/min-width
                 */
                'min-w' => [['min-w' => [$container, 'screen', 'none', ...self::getSizingScale($spacing)] ]],
                /**
                 * Max-Width
                 *
                 * @see https://tailwindcss.com/docs/max-width
                 */
                'max-w' => [
                    [
                        'max-w' => [
                            $container,
                            'screen',
                            'none',
                            'prose',
                            ['screen' => [$breakpoint]],
                            ...self::getSizingScale($spacing),
                        ],
                    ],
                ],
                /**
                 * Height
                 *
                 * @see https://tailwindcss.com/docs/height
                 */
                "h" => [[ 'h' => ['screen', ...self::getSizingScale($spacing)] ]],
                /**
                 * Min-Height
                 *
                 * @see https://tailwindcss.com/docs/min-height
                 */
                'min-h' => [
                    ['min-h' => ['screen', 'none', ...self::getSizingScale($spacing)]],
                ],
                /**
                 * Max-Height
                 *
                 * @see https://tailwindcss.com/docs/max-height
                 */
                'max-h' => [
                    ['max-h' => ['screen', ...self::getSizingScale($spacing)]],
                ],
                // ------------------
                // --- Typography ---
                // ------------------
                /**
                 * Font Size
                 *
                 * @see https://tailwindcss.com/docs/font-size
                 */
                'font-size' => [['text' => [$text]]],
                /**
                 * Font Smoothing
                 *
                 * @see https://tailwindcss.com/docs/font-smoothing
                 */
                'font-smoothing' => ['antialiased', 'subpixel-antialiased'],
                /**
                 * Font Style
                 *
                 * @see https://tailwindcss.com/docs/font-style
                 */
                'font-style' => ['italic', 'not-italic'],
                /**
                 * Font Weight
                 *
                 * @see https://tailwindcss.com/docs/font-weight
                 */
                'font-weight' => [
                    [
                        'font' => [$fontWeight, ArbitraryVariableValidator::validate(...), ArbitraryNumberValidator::validate(...)],
                    ],
                ],
                /**
                 * Font Stretch
                 * @see https://tailwindcss.com/docs/font-stretch
                 */
                'font-stretch' => [
                    [
                        'font-stretch' => [
                            'ultra-condensed',
                            'extra-condensed',
                            'condensed',
                            'semi-condensed',
                            'normal',
                            'semi-expanded',
                            'expanded',
                            'extra-expanded',
                            'ultra-expanded',
                            PercentValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Font Family
                 *
                 * @see https://tailwindcss.com/docs/font-family
                 */
                'font-family' => [['font' => [$font]]],
                /**
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-normal' => ['normal-nums'],
                /**
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-ordinal' => ['ordinal'],
                /**
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-slashed-zero' => ['slashed-zero'],
                /**
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-figure' => ['lining-nums', 'oldstyle-nums'],
                /**
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-spacing' => ['proportional-nums', 'tabular-nums'],
                /**
                 * Font Variant Numeric
                 *
                 * @see https://tailwindcss.com/docs/font-variant-numeric
                 */
                'fvn-fraction' => ['diagonal-fractions', 'stacked-fractons'],
                /**
                 * Letter Spacing
                 *
                 * @see https://tailwindcss.com/docs/letter-spacing
                 */
                'tracking' => [
                    [
                        'tracking' => [$tracking, ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)],
                    ],
                ],
                /**
                 * Line Clamp
                 *
                 * @see https://tailwindcss.com/docs/line-clamp
                 */
                'line-clamp' => [['line-clamp' => [ NumberValidator::validate(...), 'none', ArbitraryVariableValidator::validate(...), ArbitraryNumberValidator::validate(...)]]],
                /**
                 * Line Height
                 *
                 * @see https://tailwindcss.com/docs/line-height
                 */
                'leading' => [
                    ['leading' => [
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...),
                        $leading,
                        $spacing
                    ]],
                ],
                /**
                 * List Style Image
                 *
                 * @see https://tailwindcss.com/docs/list-style-image
                 */
                'list-image' => [['list-image' => ['none',ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * List Style Position
                 *
                 * @see https://tailwindcss.com/docs/list-style-position
                 */
                'list-style-position' => [['list' => ['inside', 'outside']]],
                /**
                 * List Style Type
                 *
                 * @see https://tailwindcss.com/docs/list-style-type
                 */
                'list-style-type' => [['list' => [ 'disc', 'decimal', 'none', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * Text Alignment
                 *
                 * @see https://tailwindcss.com/docs/text-align
                 */
                'text-alignment' => [['text' => ['left', 'center', 'right', 'justify', 'start', 'end']]],
                /**
                 * Placeholder Color
                 *
                 * @deprecated since Tailwind CSS v3.0.0
                 * @see https://tailwindcss.com/docs/placeholder-color
                 */
                'placeholder-color' => [['placeholder' => [$color]]],
                /**
                 * Text Color
                 *
                 * @see https://tailwindcss.com/docs/text-color
                 */
                'text-color' => [['text' => [$color]]],
                /**
                 * Text Decoration
                 *
                 * @see https://tailwindcss.com/docs/text-decoration
                 */
                'text-decoration' => ['underline', 'overline', 'line-through', 'no-underline'],
                /**
                 * Text Decoration Style
                 *
                 * @see https://tailwindcss.com/docs/text-decoration-style
                 */
                'text-decoration-style' => [['decoration' => [...self::getLineStyleScale(), 'wavy']]],
                /**
                 * Text Decoration Thickness
                 *
                 * @see https://tailwindcss.com/docs/text-decoration-thickness
                 */
                'text-decoration-thickness' => [['decoration' => [
                    NumberValidator::validate(...),
                    'from-font',
                    'auto',
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryLengthValidator::validate(...)
                ]]],
                /**
                 * Text Decoration Color
                 *
                 * @see https://tailwindcss.com/docs/text-decoration-color
                 */
                'text-decoration-color' => [['decoration' => [$color]]],
                /**
                 * Text Underline Offset
                 *
                 * @see https://tailwindcss.com/docs/text-underline-offset
                 */
                'underline-offset' => [['underline-offset' => [NumberValidator::validate(...), 'auto', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * Text Transform
                 *
                 * @see https://tailwindcss.com/docs/text-transform
                 */
                'text-transform' => ['uppercase', 'lowercase', 'capitalize', 'normal-case'],
                /**
                 * Text Overflow
                 *
                 * @see https://tailwindcss.com/docs/text-overflow
                 */
                'text-overflow' => ['truncate', 'text-ellipsis', 'text-clip'],
                /**
                 * Text Wrap
                 *
                 * @see https://tailwindcss.com/docs/text-wrap
                 */
                'text-wrap' => [['text' => ['wrap', 'nowrap', 'balance', 'pretty']]],
                /**
                 * Text Indent
                 *
                 * @see https://tailwindcss.com/docs/text-indent
                 */
                'indent' => [['indent' => ['px', ...self::getUnambiguousSpacingScale($spacing)]]],
                /**
                 * Vertical Alignment
                 *
                 * @see https://tailwindcss.com/docs/vertical-align
                 */
                'vertical-align' => [
                    [
                        'align' => [
                            'baseline',
                            'top',
                            'middle',
                            'bottom',
                            'text-top',
                            'text-bottom',
                            'sub',
                            'super',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Whitespace
                 *
                 * @see https://tailwindcss.com/docs/whitespace
                 */
                'whitespace' => [
                    ['whitespace' => ['normal', 'nowrap', 'pre', 'pre-line', 'pre-wrap', 'break-spaces']],
                ],
                /**
                 * Word Break
                 *
                 * @see https://tailwindcss.com/docs/word-break
                 */
                'break' => [['break' => ['normal', 'words', 'all', 'keep']]],
                /**
                 * Hyphens
                 *
                 * @see https://tailwindcss.com/docs/hyphens
                 */
                'hyphens' => [['hyphens' => ['none', 'manual', 'auto']]],
                /**
                 * Content
                 *
                 * @see https://tailwindcss.com/docs/content
                 */
                'content' => [['content' => ['none', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                // -------------------
                // --- Backgrounds ---
                // -------------------
                /**
                 * Background Attachment
                 *
                 * @see https://tailwindcss.com/docs/background-attachment
                 */
                'bg-attachment' => [['bg' => ['fixed', 'local', 'scroll']]],
                /**
                 * Background Clip
                 *
                 * @see https://tailwindcss.com/docs/background-clip
                 */
                'bg-clip' => [['bg-clip' => ['border', 'padding', 'content', 'text']]],
                /**
                 * Background Origin
                 *
                 * @see https://tailwindcss.com/docs/background-origin
                 */
                'bg-origin' => [['bg-origin' => ['border', 'padding', 'content']]],
                /**
                 * Background Position
                 *
                 * @see https://tailwindcss.com/docs/background-position
                 */
                'bg-position' => [['bg' => [...self::getPositionScale(), ArbitraryVariablePositionValidator::validate(...), ArbitraryPositionValidator::validate(...)]]],
                /**
                 * Background Repeat
                 *
                 * @see https://tailwindcss.com/docs/background-repeat
                 */
                'bg-repeat' => [['bg' => ['no-repeat', ['repeat' => ['', 'x', 'y', 'space', 'round']]]]],
                /**
                 * Background Size
                 *
                 * @see https://tailwindcss.com/docs/background-size
                 */
                'bg-size' => [['bg' => ['auto', 'cover', 'contain', ArbitraryVariableSizeValidator::validate(...), ArbitrarySizeValidator::validate(...)]]],
                /**
                 * Background Image
                 *
                 * @see https://tailwindcss.com/docs/background-image
                 */
                'bg-image' => [
                    [
                        'bg' => [
                            'none',
                            [
                                'linear' => [
                                    ['to' => ['t', 'tr', 'r', 'br', 'b', 'bl', 'l', 'tl']],
                                    IntegerValidator::validate(...),
                                    ArbitraryVariableValidator::validate(...),
                                    ArbitraryValueValidator::validate(...),
                                ],
                                'radial' => ['', ArbitraryVariableValidator::validate(...),ArbitraryValueValidator::validate(...)],
                                'conic' => [IntegerValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]
                            ],
                            ArbitraryVariableImageValidator::validate(...),
                            ArbitraryImageValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Background Color
                 *
                 * @see https://tailwindcss.com/docs/background-color
                 */
                'bg-color' => [['bg' => [$color]]],
                /**
                 * Gradient Color Stops From Position
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-from-pos' => [['from' => self::getGradientStopPositionScale()]],
                /**
                 * Gradient Color Stops Via Position
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-via-pos' => [['via' => self::getGradientStopPositionScale()]],
                /**
                 * Gradient Color Stops To Position
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-to-pos' => [['to' => self::getGradientStopPositionScale()]],
                /**
                 * Gradient Color Stops From
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-from' => [['from' => [$color]]],
                /**
                 * Gradient Color Stops Via
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-via' => [['via' => [$color]]],
                /**
                 * Gradient Color Stops To
                 *
                 * @see https://tailwindcss.com/docs/gradient-color-stops
                 */
                'gradient-to' => [['to' => [$color]]],
                // ---------------
                // --- Borders ---
                // ---------------
                /**
                 * Border Radius
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded' => [['rounded' => [$radius]]],
                /**
                 * Border Radius Start
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-s' => [['rounded-s' => [$radius]]],
                /**
                 * Border Radius End
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-e' => [['rounded-e' => [$radius]]],
                /**
                 * Border Radius Top
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-t' => [['rounded-t' => [$radius]]],
                /**
                 * Border Radius Right
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-r' => [['rounded-r' => [$radius]]],
                /**
                 * Border Radius Bottom
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-b' => [['rounded-b' => [$radius]]],
                /**
                 * Border Radius Left
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-l' => [['rounded-l' => [$radius]]],
                /**
                 * Border Radius Start Start
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-ss' => [['rounded-ss' => [$radius]]],
                /**
                 * Border Radius Start End
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-se' => [['rounded-se' => [$radius]]],
                /**
                 * Border Radius End End
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-ee' => [['rounded-ee' => [$radius]]],
                /**
                 * Border Radius End Start
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-es' => [['rounded-es' => [$radius]]],
                /**
                 * Border Radius Top Left
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-tl' => [['rounded-tl' => [$radius]]],
                /**
                 * Border Radius Top Right
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-tr' => [['rounded-tr' => [$radius]]],
                /**
                 * Border Radius Bottom Right
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-br' => [['rounded-br' => [$radius]]],
                /**
                 * Border Radius Bottom Left
                 *
                 * @see https://tailwindcss.com/docs/border-radius
                 */
                'rounded-bl' => [['rounded-bl' => [$radius]]],
                /**
                 * Border Width
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w' => [['border' => self::getBorderWidthScale()]],
                /**
                 * Border Width X
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-x' => [['border-x' => self::getBorderWidthScale()]],
                /**
                 * Border Width Y
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-y' => [['border-y' => self::getBorderWidthScale()]],
                /**
                 * Border Width Start
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-s' => [['border-s' => self::getBorderWidthScale()]],
                /**
                 * Border Width End
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-e' => [['border-e' => self::getBorderWidthScale()]],
                /**
                 * Border Width Top
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-t' => [['border-t' => self::getBorderWidthScale()]],
                /**
                 * Border Width Right
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-r' => [['border-r' => self::getBorderWidthScale()]],
                /**
                 * Border Width Bottom
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-b' => [['border-b' => self::getBorderWidthScale()]],
                /**
                 * Border Width Left
                 *
                 * @see https://tailwindcss.com/docs/border-width
                 */
                'border-w-l' => [['border-l' => self::getBorderWidthScale()]],
                /**
                 * Divide Width X
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-x' => [['divide-x' => self::getBorderWidthScale()]],
                /**
                 * Divide Width X Reverse
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-x-reverse' => ['divide-x-reverse'],
                /**
                 * Divide Width Y
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-y' => [['divide-y' => self::getBorderWidthScale()]],
                /**
                 * Divide Width Y Reverse
                 *
                 * @see https://tailwindcss.com/docs/border-width#between-children
                 */
                'divide-y-reverse' => ['divide-y-reverse'],
                /**
                 * Border Style
                 *
                 * @see https://tailwindcss.com/docs/border-style
                 */
                'border-style' => [['border' => [...self::getLineStyleScale(), 'hidden', 'none']]],
                /**
                 * Divide Style
                 *
                * @see https://tailwindcss.com/docs/border-style#setting-the-divider-style
                * */
                'divide-style' => [['divide' => [...self::getLineStyleScale(), 'hidden', 'none']]],
                /**
                 * Border Color
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color' => [
                    [
                        'border' => [$color],
                    ],
                ],
                /**
                 * Border Color X
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-x' => [['border-x' => [$color]]],
                /**
                 * Border Color Y
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-y' => [['border-y' => [$color]]],
                /**
                 * Border Color Top
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-t' => [['border-t' => [$color]]],
                /**
                 * Border Color Right
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-r' => [['border-r' => [$color]]],
                /**
                 * Border Color Bottom
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-b' => [['border-b' => [$color]]],
                /**
                 * Border Color Left
                 *
                 * @see https://tailwindcss.com/docs/border-color
                 */
                'border-color-l' => [['border-l' => [$color]]],
                /**
                 * Divide Color
                 *
                 * @see https://tailwindcss.com/docs/divide-color
                 */
                'divide-color' => [['divide' => [$color]]],
                /**
                 * Outline Style
                 *
                 * @see https://tailwindcss.com/docs/outline-style
                 */
                'outline-style' => [['outline' => [...self::getLineStyleScale(), 'none', 'hidden']]],
                /**
                 * Outline Offset
                 *
                 * @see https://tailwindcss.com/docs/outline-offset
                 */
                'outline-offset' => [['outline-offset' => [NumberValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * Outline Width
                 *
                 * @see https://tailwindcss.com/docs/outline-width
                 */
                'outline-w' => [['outline' => [NumberValidator::validate(...),ArbitraryVariableValidator::validate(...), ArbitraryLengthValidator::validate(...)]]],
                /**
                 * Outline Color
                 *
                 * @see https://tailwindcss.com/docs/outline-color
                 */
                'outline-color' => [['outline' => [$color]]],
                // ---------------
                // --- Effects ---
                // ---------------
                /**
                 * Box Shadow
                 * @see https://tailwindcss.com/docs/box-shadow
                 */
                'shadow' => [
                    [
                        'shadow' => [
                            // Deprecated since Tailwind CSS v4.0.0
                            '',
                            $shadow,
                        ]
                    ]
                ],
                /**
                 * Box Shadow Color
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-shadow-color
                 */
                'shadow-color' => [[ 'shadow' => [$color] ]],
                /**
                 * Inset Box Shadow
                 * @see https://tailwindcss.com/docs/box-shadow#adding-an-inset-shadow
                 */
                'inset-shadow' => [[ 'inset-shadow' => ['none', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...), $insetShadow] ]],
                /**
                 * Inset Box Shadow Color
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-inset-shadow-color
                 */
                'inset-shadow-color' => [[ 'inset-shadow' => [$color] ]],
                /**
                 * Ring Width
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#adding-a-ring
                */
                'ring-w' => [['ring' => self::getBorderWidthScale()]],
                /**
                 * Ring Width Inset
                 *
                 * @see https://v3.tailwindcss.com/docs/ring-width#inset-rings
                 * @deprecated since Tailwind CSS v4.0.0
                 * @see https://github.com/tailwindlabs/tailwindcss/blob/v4.0.0/packages/tailwindcss/src/utilities.ts#L4158
                */
                'ring-w-inset' => ['ring-inset'],
                /**
                 * Ring Color
                 *
                 * @see https://tailwindcss.com/docs/box-shadow#setting-the-ring-color
                */
                'ring-color' => [['ring' => [$color]]],
                /**
                 * Ring Offset Width
                 *
                 * @see https://v3.tailwindcss.com/docs/ring-offset-width
                 * @deprecated since Tailwind CSS v4.0.0
                 * @see https://github.com/tailwindlabs/tailwindcss/blob/v4.0.0/packages/tailwindcss/src/utilities.ts#L4158
                */
                'ring-offset-w' => [['ring-offset' => [NumberValidator::validate(...), ArbitraryLengthValidator::validate(...)]]],
                /**
                 * Ring Offset Color
                 *
                 * @see https://v3.tailwindcss.com/docs/ring-offset-color
                 * @deprecated since Tailwind CSS v4.0.0
                 * @see https://github.com/tailwindlabs/tailwindcss/blob/v4.0.0/packages/tailwindcss/src/utilities.ts#L4158
                */
                'ring-offset-color' => [['ring-offset' => [$color]]],
                /**
                 * Inset Ring Width
                 * @see https://tailwindcss.com/docs/box-shadow#adding-an-inset-ring
                */
                'inset-ring-w' => [['inset-ring' => self::getBorderWidthScale()]],
                /**
                * Inset Ring Color
                * @see https://tailwindcss.com/docs/box-shadow#setting-the-inset-ring-color
                */
                'inset-ring-color' => [['inset-ring' => [$color]]],
                /**
                 * Opacity
                 *
                 * @see https://tailwindcss.com/docs/opacity
                */
                'opacity' => [['opacity' => [NumberValidator::validate(...), ArbitraryValueValidator::validate(...), ArbitraryValueValidator::validate(...)]]],
                /**
                 * Mix Blend Mode
                 *
                 * @see https://tailwindcss.com/docs/mix-blend-mode
                */
                'mix-blend' => [['mix-blend' => [...self::getBlendModeScale(), 'plus-darker', 'plus-lighter']]],
                /**
                 * Background Blend Mode
                 *
                 * @see https://tailwindcss.com/docs/background-blend-mode
                */
                'bg-blend' => [['bg-blend' => self::getBlendModeScale()]],
                // ---------------
                // --- Filters ---
                // ---------------
                /**
                 * Filter
                 *
                 * @see https://tailwindcss.com/docs/filter
                 */
                'filter' => [[
                    // Deprecated since Tailwind CSS v3.0.0
                    'filter' => ['', 'none', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]
                ]],
                /**
                 * Blur
                 *
                 * @see https://tailwindcss.com/docs/blur
                 */
                'blur' => [['blur' => [$blur]]],
                /**
                 * Brightness
                 *
                 * @see https://tailwindcss.com/docs/brightness
                 */
                'brightness' => [['brightness' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Contrast
                 *
                 * @see https://tailwindcss.com/docs/contrast
                 */
                'contrast' => [['contrast' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Drop Shadow
                 *
                 * @see https://tailwindcss.com/docs/drop-shadow
                 */
                'drop-shadow' => [
                    [
                        'drop-shadow' => [$dropShadow]
                    ]
                ],
                /**
                 * Grayscale
                 *
                 * @see https://tailwindcss.com/docs/grayscale
                 */
                'grayscale' => [['grayscale' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Hue Rotate
                 *
                 * @see https://tailwindcss.com/docs/hue-rotate
                 */
                'hue-rotate' => [['hue-rotate' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Invert
                 *
                 * @see https://tailwindcss.com/docs/invert
                 */
                'invert' => [['invert' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Saturate
                 *
                 * @see https://tailwindcss.com/docs/saturate
                 */
                'saturate' => [['saturate' => [
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Sepia
                 *
                 * @see https://tailwindcss.com/docs/sepia
                 */
                'sepia' => [['sepia' => [
                    '',
                    NumberValidator::validate(...),
                    ArbitraryVariableValidator::validate(...),
                    ArbitraryValueValidator::validate(...),
                ]]],
                /**
                 * Backdrop Filter
                 *
                 * @see https://tailwindcss.com/docs/backdrop-filter
                 */
                'backdrop-filter' => [[
                    'backdrop-filter' => [
                        // @deprecated since Tailwind CSS v3.0.0
                        '',
                        'none',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...),
                    ]
                ]],
                /**
                 * Backdrop Blur
                 *
                 * @see https://tailwindcss.com/docs/backdrop-blur
                 */
                'backdrop-blur' => [['backdrop-blur' => [$blur]]],
                /**
                 * Backdrop Brightness
                 *
                 * @see https://tailwindcss.com/docs/backdrop-brightness
                 */
                'backdrop-brightness' => [['backdrop-brightness' =>
                    [
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Contrast
                 *
                 * @see https://tailwindcss.com/docs/backdrop-contrast
                 */
                'backdrop-contrast' => [['backdrop-contrast' =>
                    [
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Grayscale
                 *
                 * @see https://tailwindcss.com/docs/backdrop-grayscale
                 */
                'backdrop-grayscale' => [['backdrop-grayscale' =>
                    [
                        '',
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Hue Rotate
                 *
                 * @see https://tailwindcss.com/docs/backdrop-hue-rotate
                 */
                'backdrop-hue-rotate' => [['backdrop-hue-rotate' =>
                    [
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Invert
                 *
                 * @see https://tailwindcss.com/docs/backdrop-invert
                 */
                'backdrop-invert' => [['backdrop-invert' =>
                    [
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Opacity
                 *
                 * @see https://tailwindcss.com/docs/backdrop-opacity
                 */
                'backdrop-opacity' => [['backdrop-opacity' =>
                    [
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Saturate
                 *
                 * @see https://tailwindcss.com/docs/backdrop-saturate
                 */
                'backdrop-saturate' => [['backdrop-saturate' =>
                    [
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Backdrop Sepia
                 *
                 * @see https://tailwindcss.com/docs/backdrop-sepia
                 */
                'backdrop-sepia' => [['backdrop-sepia' =>
                    [
                        '',
                        NumberValidator::validate(...),
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                // --------------
                // --- Tables ---
                // --------------
                /**
                 * Border Collapse
                 *
                 * @see https://tailwindcss.com/docs/border-collapse
                 */
                'border-collapse' => [['border' => ['collapse', 'separate']]],
                /**
                 * Border Spacing
                 *
                 * @see https://tailwindcss.com/docs/border-spacing
                 */
                'border-spacing' => [['border-spacing' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Border Spacing X
                 *
                 * @see https://tailwindcss.com/docs/border-spacing
                 */
                'border-spacing-x' => [['border-spacing-x' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Border Spacing Y
                 *
                 * @see https://tailwindcss.com/docs/border-spacing
                 */
                'border-spacing-y' => [['border-spacing-y' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Table Layout
                 *
                 * @see https://tailwindcss.com/docs/table-layout
                 */
                'table-layout' => [['table' => ['auto', 'fixed']]],
                /**
                 * Caption Side
                 *
                 * @see https://tailwindcss.com/docs/caption-side
                 */
                'caption' => [['caption' => ['top', 'bottom']]],
                // ---------------------------------
                // --- Transitions and Animation ---
                // ---------------------------------
                /**
                 * Transition Property
                 *
                 * @see https://tailwindcss.com/docs/transition-property
                 */
                'transition' => [
                    [
                        'transition' => [
                            '',
                            'all',
                            'colors',
                            'opacity',
                            'shadow',
                            'transform',
                            'none',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Transition Behavior
                 * @see https://tailwindcss.com/docs/transition-behavior
                 */
                'transition-behavior' => [
                    [
                        'transition' => ['normal', 'discrete'],
                    ],
                ],
                /**
                 * Transition Duration
                 *
                 * @see https://tailwindcss.com/docs/transition-duration
                 */
                'duration' => [['duration' =>
                    [
                        NumberValidator::validate(...),
                        'initial',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Transition Timing Function
                 *
                 * @see https://tailwindcss.com/docs/transition-timing-function
                 */
                'ease' => [['ease' =>
                    [
                        'linear',
                        'initial',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...),
                        $ease
                    ]
                ]],
                /**
                 * Transition Delay
                 *
                 * @see https://tailwindcss.com/docs/transition-delay
                 */
                'delay' => [['delay' =>
                    [
                        NumberValidator::validate(...),
                        'initial',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                /**
                 * Animation
                 *
                 * @see https://tailwindcss.com/docs/animation
                 */
                'animate' => [['animate' =>
                    [
                        'none',
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...),
                        $animate
                    ]
                ]],
                // ------------------
                // --- Transforms ---
                // ------------------
                /**
                 * Backface Visibility
                 * @see https://tailwindcss.com/docs/backface-visibility
                */
                'backface' => [
                    [
                        'backface' => ['hidden', 'visible'],
                    ],
                ],
                /**
                 * Perspective
                 * @see https://tailwindcss.com/docs/perspective
                */
                'perspective' => [['perspective' =>
                    [
                        $perspective,
                        ArbitraryVariableValidator::validate(...),
                        ArbitraryValueValidator::validate(...)
                    ]
                ]],
                 /**
                 * Perspective Origin
                 * @see https://tailwindcss.com/docs/perspective-origin
                 */
                'perspective-origin' => [['perspective-origin' => self::getOriginScale()]],
                /**
                 * Rotate
                 *
                 * @see https://tailwindcss.com/docs/rotate
                 */
                'rotate' => [['rotate' => self::getRotateScale()]],
                'rotate-x' => [['rotate-x' => self::getRotateScale()]],
                'rotate-y' => [['rotate-y' => self::getRotateScale()]],
                'rotate-z' => [['rotate-z' => self::getRotateScale()]],
                /**
                 * Scale
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale' => [
                    [
                        'scale' => self::getScaleScale(),
                    ],
                ],
                 /**
                 * Scale X
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-x' => [
                    [
                        'scale-x' => self::getScaleScale(),
                    ],
                ],

                /**
                 * Scale Y
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-y' => [
                    [
                        'scale-y' => self::getScaleScale(),
                    ],
                ],

                /**
                 * Scale Z
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-z' => [
                    [
                        'scale-z' => self::getScaleScale(),
                    ],
                ],

                /**
                 * Scale 3D
                 * @see https://tailwindcss.com/docs/scale
                 */
                'scale-3d' => ['scale-3d'],
                /**
                 * Skew
                 *
                 * @see https://tailwindcss.com/docs/skew
                 */
                'skew' => [['skew' => self::getSkewScale($spacing)]],
                /**
                 * Skew X
                 *
                 * @see https://tailwindcss.com/docs/skew
                 */
                'skew-x' => [['skew-x' => self::getSkewScale($spacing)]],
                /**
                 * Skew Y
                 *
                 * @see https://tailwindcss.com/docs/skew
                 */
                'skew-y' => [['skew-y' => self::getSkewScale($spacing)]],
                 /**
                 * Transform
                 * @see https://tailwindcss.com/docs/transform
                 */
                'transform' => [
                    [
                        'transform' => [
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                            '',
                            'none',
                            'gpu',
                            'cpu',
                        ],
                    ],
                ],
                /**
                 * Transform Origin
                 *
                 * @see https://tailwindcss.com/docs/transform-origin
                 */
                'transform-origin' => [
                    [
                        'origin' => self::getOriginScale(),
                    ],
                ],
                /**
                 * Transform Style
                 * @see https://tailwindcss.com/docs/transform-style
                 */
                'transform-style' => [[ 'transform' => ['3d', 'flat']]],
                /**
                 * Translate
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate' => [
                    [
                        'translate' => self::getTranslateScale($spacing),
                    ],
                ],

                /**
                 * Translate X
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-x' => [
                    [
                        'translate-x' => self::getTranslateScale($spacing),
                    ],
                ],

                /**
                 * Translate Y
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-y' => [
                    [
                        'translate-y' => self::getTranslateScale($spacing),
                    ],
                ],

                /**
                 * Translate Z
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-z' => [
                    [
                        'translate-z' => self::getTranslateScale($spacing),
                    ],
                ],

                /**
                 * Translate None
                 * @see https://tailwindcss.com/docs/translate
                 */
                'translate-none' => ['translate-none'],
                // ---------------------
                // --- Interactivity ---
                // ---------------------
                /*
                 * Accent Color
                 *
                 * @see https://tailwindcss.com/docs/accent-color
                 */
                'accent' => [['accent' => [$color]]],
                /**
                 * Appearance
                 *
                 * @see https://tailwindcss.com/docs/appearance
                 */
                'appearance' => [['appearance' => ['none', 'auto']]],
                /**
                 * Caret Color
                 * @see https://tailwindcss.com/docs/just-in-time-mode#caret-color-utilities
                 */
                'caret-color' => [
                    ['caret' => [$color]],
                ],
                /**
                 * Color Scheme
                 * @see https://tailwindcss.com/docs/color-scheme
                 */
                'color-scheme' => [
                    ['scheme' => ['normal', 'dark', 'light', 'light-dark', 'only-dark', 'only-light']],
                ],
                /**
                 * Cursor
                 *
                 * @see https://tailwindcss.com/docs/cursor
                 */
                'cursor' => [
                    [
                        'cursor' => [
                            'auto',
                            'default',
                            'pointer',
                            'wait',
                            'text',
                            'move',
                            'help',
                            'not-allowed',
                            'none',
                            'context-menu',
                            'progress',
                            'cell',
                            'crosshair',
                            'vertical-text',
                            'alias',
                            'copy',
                            'no-drop',
                            'grab',
                            'grabbing',
                            'all-scroll',
                            'col-resize',
                            'row-resize',
                            'n-resize',
                            'e-resize',
                            's-resize',
                            'w-resize',
                            'ne-resize',
                            'nw-resize',
                            'se-resize',
                            'sw-resize',
                            'ew-resize',
                            'ns-resize',
                            'nesw-resize',
                            'nwse-resize',
                            'zoom-in',
                            'zoom-out',
                            ArbitraryVariableValidator::validate(...),
                            ArbitraryValueValidator::validate(...),
                        ],
                    ],
                ],
                /**
                 * Field Sizing
                 * @see https://tailwindcss.com/docs/field-sizing
                 */
                'field-sizing' => [[ 'field-sizing' => ['fixed', 'content'] ]],
                /**
                 * Pointer Events
                 *
                 * @see https://tailwindcss.com/docs/pointer-events
                 */
                'pointer-events' => [['pointer-events' => ['auto', 'none']]],
                /**
                 * Resize
                 *
                 * @see https://tailwindcss.com/docs/resize
                 */
                'resize' => [['resize' => ['none', '', 'y', 'x']]],
                /**
                 * Scroll Behavior
                 *
                 * @see https://tailwindcss.com/docs/scroll-behavior
                 */
                'scroll-behavior' => [['scroll' => ['auto', 'smooth']]],
                /**
                 * Scroll Margin
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-m' => [['scroll-m' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin X
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mx' => [['scroll-mx' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin Y
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-my' => [['scroll-my' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin Start
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-ms' => [['scroll-ms' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin End
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-me' => [['scroll-me' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin Top
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mt' => [['scroll-mt' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin Right
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mr' => [['scroll-mr' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin Bottom
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-mb' => [['scroll-mb' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Margin Left
                 *
                 * @see https://tailwindcss.com/docs/scroll-margin
                 */
                'scroll-ml' => [['scroll-ml' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-p' => [['scroll-p' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding X
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-px' => [['scroll-px' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding Y
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-py' => [['scroll-py' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding Start
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-ps' => [['scroll-ps' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding End
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pe' => [['scroll-pe' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding Top
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pt' => [['scroll-pt' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding Right
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pr' => [['scroll-pr' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding Bottom
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pb' => [['scroll-pb' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Padding Left
                 *
                 * @see https://tailwindcss.com/docs/scroll-padding
                 */
                'scroll-pl' => [['scroll-pl' => self::getUnambiguousSpacingScale($spacing)]],
                /**
                 * Scroll Snap Align
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-align
                 */
                'snap-align' => [['snap' => ['start', 'end', 'center', 'align-none']]],
                /**
                 * Scroll Snap Stop
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-stop
                 */
                'snap-stop' => [['snap' => ['normal', 'always']]],
                /**
                 * Scroll Snap Type
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-type
                 */
                'snap-type' => [['snap' => ['none', 'x', 'y', 'both']]],
                /**
                 * Scroll Snap Type Strictness
                 *
                 * @see https://tailwindcss.com/docs/scroll-snap-type
                 */
                'snap-strictness' => [['snap' => ['mandatory', 'proximity']]],
                /**
                 * Touch Action
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch' => [
                    [
                        'touch' => [
                            'auto',
                            'none',
                            'manipulation',
                        ],
                    ],
                ],
                /**
                 * Touch Action X
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch-x' => [
                    [
                        'touch-pan' => ['x', 'left', 'right'],
                    ],
                ],
                /**
                 * Touch Action Y
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch-y' => [
                    [
                        'touch-pan' => ['y', 'up', 'down'],
                    ],
                ],
                /**
                 * Touch Action Pinch Zoom
                 *
                 * @see https://tailwindcss.com/docs/touch-action
                 */
                'touch-pz' => ['touch-pinch-zoom'],
                /**
                 * User Select
                 *
                 * @see https://tailwindcss.com/docs/user-select
                 */
                'select' => [['select' => ['none', 'text', 'all', 'auto']]],
                /**
                 * Will Change
                 *
                 * @see https://tailwindcss.com/docs/will-change
                 */
                'will-change' => [
                    ['will-change' => ['auto', 'scroll', 'contents', 'transform', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]],
                ],
                // -----------
                // --- SVG ---
                // -----------
                /**
                 * Fill
                 *
                 * @see https://tailwindcss.com/docs/fill
                 */
                'fill' => [['fill' => ['none', $color]]],
                /**
                 * Stroke Width
                 *
                 * @see https://tailwindcss.com/docs/stroke-width
                 */
                'stroke-w' => [['stroke' => [NumberValidator::validate(...),  ArbitraryVariableValidator::validate(...),ArbitraryLengthValidator::validate(...), ArbitraryNumberValidator::validate(...)]]],
                /**
                 * Stroke
                 *
                 * @see https://tailwindcss.com/docs/stroke
                 */
                'stroke' => [['stroke' => ['none', $color]]],
                // ---------------------
                // --- Accessibility ---
                // ---------------------
                /**
                 * Forced Color Adjust
                 *
                 * @see https://tailwindcss.com/docs/forced-color-adjust
                 */
                'forced-color-adjust' => [['forced-color-adjust' => ['auto', 'none']]],
            ],
            'conflictingClassGroups' => [
                'overflow' => ['overflow-x', 'overflow-y'],
                'overscroll' => ['overscroll-x', 'overscroll-y'],
                'inset' => ['inset-x', 'inset-y', 'start', 'end', 'top', 'right', 'bottom', 'left'],
                'inset-x' => ['right', 'left'],
                'inset-y' => ['top', 'bottom'],
                'flex' => ['basis', 'grow', 'shrink'],
                'gap' => ['gap-x', 'gap-y'],
                'p' => ['px', 'py', 'ps', 'pe', 'pt', 'pr', 'pb', 'pl'],
                'px' => ['pr', 'pl'],
                'py' => ['pt', 'pb'],
                'm' => ['mx', 'my', 'ms', 'me', 'mt', 'mr', 'mb', 'ml'],
                'mx' => ['mr', 'ml'],
                'my' => ['mt', 'mb'],
                'size' => ['w', 'h'],
                'font-size' => ['leading'],
                'fvn-normal' => [
                    'fvn-ordinal',
                    'fvn-slashed-zero',
                    'fvn-figure',
                    'fvn-spacing',
                    'fvn-fraction',
                ],
                'fvn-ordinal' => ['fvn-normal'],
                'fvn-slashed-zero' => ['fvn-normal'],
                'fvn-figure' => ['fvn-normal'],
                'fvn-spacing' => ['fvn-normal'],
                'fvn-fraction' => ['fvn-normal'],
                'line-clamp' => ['display', 'overflow'],
                'rounded' => [
                    'rounded-s',
                    'rounded-e',
                    'rounded-t',
                    'rounded-r',
                    'rounded-b',
                    'rounded-l',
                    'rounded-ss',
                    'rounded-se',
                    'rounded-ee',
                    'rounded-es',
                    'rounded-tl',
                    'rounded-tr',
                    'rounded-br',
                    'rounded-bl',
                ],
                'rounded-s' => ['rounded-ss', 'rounded-es'],
                'rounded-e' => ['rounded-se', 'rounded-ee'],
                'rounded-t' => ['rounded-tl', 'rounded-tr'],
                'rounded-r' => ['rounded-tr', 'rounded-br'],
                'rounded-b' => ['rounded-br', 'rounded-bl'],
                'rounded-l' => ['rounded-tl', 'rounded-bl'],
                'border-spacing' => ['border-spacing-x', 'border-spacing-y'],
                'border-w' => [
                    'border-w-s',
                    'border-w-e',
                    'border-w-t',
                    'border-w-r',
                    'border-w-b',
                    'border-w-l',
                ],
                'border-w-x' => ['border-w-r', 'border-w-l'],
                'border-w-y' => ['border-w-t', 'border-w-b'],
                'border-color' => [
                    'border-color-t',
                    'border-color-r',
                    'border-color-b',
                    'border-color-l',
                ],
                'border-color-x' => ['border-color-r', 'border-color-l'],
                'border-color-y' => ['border-color-t', 'border-color-b'],
                'translate' => ['translate-x', 'translate-y', 'translate-none'],
                'translate-none' => ['translate', 'translate-x', 'translate-y', 'translate-z'],
                'scroll-m' => [
                    'scroll-mx',
                    'scroll-my',
                    'scroll-ms',
                    'scroll-me',
                    'scroll-mt',
                    'scroll-mr',
                    'scroll-mb',
                    'scroll-ml',
                ],
                'scroll-mx' => ['scroll-mr', 'scroll-ml'],
                'scroll-my' => ['scroll-mt', 'scroll-mb'],
                'scroll-p' => [
                    'scroll-px',
                    'scroll-py',
                    'scroll-ps',
                    'scroll-pe',
                    'scroll-pt',
                    'scroll-pr',
                    'scroll-pb',
                    'scroll-pl',
                ],
                'scroll-px' => ['scroll-pr', 'scroll-pl'],
                'scroll-py' => ['scroll-pt', 'scroll-pb'],
                'touch' => ['touch-x', 'touch-y', 'touch-pz'],
                'touch-x' => ['touch'],
                'touch-y' => ['touch'],
                'touch-pz' => ['touch'],
            ],
            'conflictingClassGroupModifiers' => [
                'font-size' => ['leading'],
            ],
        ];
    }

    public static function fromTheme(string $key): ThemeGetter
    {
        return new ThemeGetter($key);
    }

    /**
     * @return array<int, callable>
     */
    private static function getNumber(): array
    {
        return [
            NumberValidator::validate(...),
            ArbitraryNumberValidator::validate(...),
        ];
    }

    /**
     * @return array<int, string|callable|ThemeGetter>
     */
    private static function getSpacingWithAutoAndArbitrary(ThemeGetter $spacing): array
    {
        return [
            'auto',
            ArbitraryValueValidator::validate(...),
            $spacing,
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getPositions(): array
    {
        return [
            'bottom',
            'center',
            'left',
            'left-bottom',
            'left-top',
            'right',
            'right-bottom',
            'right-top',
            'top',
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getBreakScale(): array
    {
        return [
            'auto', 'avoid', 'all', 'avoid-page', 'page', 'left', 'right', 'column'
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getPositionScale(): array
    {
        return [
            'bottom',
            'center',
            'left',
            'left-bottom',
            'left-top',
            'right',
            'right-bottom',
            'right-top',
            'top',
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getOverflowScale(): array
    {
        return [
            'auto', 'hidden', 'clip', 'visible', 'scroll'
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getOverscrollScale(): array
    {
        return [
            'auto', 'contain', 'none'
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getInsetScale(ThemeGetter $spacing): array
    {
        return [
            FractionValidator::validate(...), 'px', 'full', 'auto', ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...), $spacing
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getGridTemplateColsRowsScale(): array
    {
        return [
            IntegerValidator::validate(...), 'none', 'subgrid',  ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getGridColRowStartAndEndScale(): array
    {
        return [
            'auto',
            ['span' => ['full', IntegerValidator::validate(...), ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...)]],
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getGridColRowStartOrEndScale(): array
    {
        return [
            IntegerValidator::validate(...),
            'auto',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getTranslateScale(ThemeGetter $spacing): array
    {
        return [
            FractionValidator::validate(...),
            'full',
            'px',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $spacing
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getGridAutoColsRowsScale(): array
    {
        return [
            'auto',
            'min',
            'max',
            'fr',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getGapScale(ThemeGetter $spacing): array
    {
        return [
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $spacing
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getAlignPrimaryAxisScale(): array
    {
        return [
            'start', 'end', 'center', 'between', 'around', 'evenly', 'stretch', 'baseline'
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getAlignSecondaryAxisScale(): array
    {
        return ['start', 'end', 'center', 'stretch'];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getUnambiguousSpacingScale(ThemeGetter $spacing): array
    {
        return [ArbitraryVariableValidator::validate(...), ArbitraryValueValidator::validate(...), $spacing];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getMarginScale(ThemeGetter $spacing): array
    {
        return ['auto', ...self::getUnambiguousSpacingScale($spacing)];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getSizingScale(ThemeGetter $spacing): array
    {
        return [
            FractionValidator::validate(...),
            'auto',
            'px',
            'full',
            'dvw',
            'dvh',
            'lvw',
            'lvh',
            'svw',
            'svh',
            'min',
            'max',
            'fit',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $spacing
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getGradientStopPositionScale(): array
    {
        return [
            PercentValidator::validate(...),
            ArbitraryLengthValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getBorderWidthScale(): array
    {
        return [
            '',
            NumberValidator::validate(...),
            ArbitraryVariableLengthValidator::validate(...),
            ArbitraryLengthValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string|callable>
     */
    private static function getLineStyleScale(): array
    {
        return ['solid', 'dashed', 'dotted', 'double'];
    }

    /**
     * @return array<int, string>
     */
    private static function getLineStyles(): array
    {
        return [
            'solid',
            'dashed',
            'dotted',
            'double',
            'none',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getBlendModeScale(): array
    {
        return [
            'normal',
            'multiply',
            'screen',
            'overlay',
            'darken',
            'lighten',
            'color-dodge',
            'color-burn',
            'hard-light',
            'soft-light',
            'difference',
            'exclusion',
            'hue',
            'saturation',
            'color',
            'luminosity',
            'plus-lighter',
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getOriginScale(): array
    {
        return [
            'center',
            'top',
            'top-right',
            'right',
            'bottom-right',
            'bottom',
            'bottom-left',
            'left',
            'top-left',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getRotateScale(): array
    {
        return [
            'none',
            NumberValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getScaleScale(): array
    {
        return [
            'none',
            NumberValidator::validate(...),
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...)
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function getSkewScale(ThemeGetter $spacing): array
    {
        return [
            FractionValidator::validate(...),
            'full', 'px',
            ArbitraryVariableValidator::validate(...),
            ArbitraryValueValidator::validate(...),
            $spacing
        ];
    }
}
