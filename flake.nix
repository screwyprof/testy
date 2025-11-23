{
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    phps.url = "github:fossar/nix-phps";
    flake-parts.url = "github:hercules-ci/flake-parts";
    flake-parts.inputs.nixpkgs-lib.follows = "nixpkgs";
  };

  outputs = inputs@{ flake-parts, ... }:
    flake-parts.lib.mkFlake { inherit inputs; } {
      systems = [ "x86_64-linux" "aarch64-linux" "x86_64-darwin" "aarch64-darwin" ];

      perSystem = { config, self', inputs', pkgs, system, ... }: {
        # Apply nix-phps overlay to get access to their PHP packages and tools
        legacyPackages = import inputs.nixpkgs {
          inherit system;
          overlays = [ inputs.phps.overlays.default ];
        };

        devShells.default =
          let
            phpsPkgs = config.legacyPackages;
          in
          phpsPkgs.mkShell {
            buildInputs = with phpsPkgs; [
              # Use PHP 8.3 from nix-phps
              php83

              # Use Composer from nix-phps (compatible with their PHP builds)
              php83.packages.composer

              # Formatters
              php83.packages.php-cs-fixer

              # Additional useful tools
              git
            ];

            shellHook = ''
              echo "🐘 PHP Legacy Project Environment"
              echo "=================================="
              php --version
              composer --version
              echo ""
              echo "Quick start:"
              echo "  composer install    # Install dependencies"
              echo "  php -S localhost:8000  # Start development server"
              echo "  php index.php       # Run the application"
              echo ""
            '';
          };
      };
    };
}